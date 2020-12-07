<?php

namespace Lib;


use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;


abstract class AbstractController
{
    protected Request $request;

    protected Session $session;


    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->session = new Session();
        $this->session->start();
    }


    /**
     *  Twig render system
     *
     * @param string $view
     * @param array $params
     * @return Response
     */
    public function render(string $view, array $params): Response
    {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $twig = new Environment($loader, [
            'cache' => false,
            'debug' => true
        ]);

        $twig->addFunction(new TwigFunction('asset', function ($asset) {
            return sprintf('../assets/%s', ltrim($asset, '/'));
        }));

        $twig->addExtension(new DebugExtension());

        $twig->addGlobal('session', $this->session->all());

        $categories = new CategoryRepository();
        $twig->addGlobal('global_categories', $categories->findAll());

        $render = $twig->render($view, $params);

        return new Response($render, 200, []);
    }


    /**
     * Redirection
     *
     * @param string $uri
     */
    public function redirectToRoute(string $uri)
    {
        header('Location: '.$uri);
    }


    /**
     * Remove special characters and format a slug
     *
     * @param string $title
     * @return string
     */
    public function formatSlug(string $title) : string
    {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
        $slug = strtolower($slug);
        $slug = preg_replace("/([^a-z0-9]+)/", "-", $slug);
        $slug = $slug . '-';
        $slug = preg_replace("/-{2,}/", "-", $slug);

        return $slug;
    }


    /**
     * Upload File
     *
     * @param UploadedFile $uploadedFile
     * @return string
     */
    public function uploadFile(UploadedFile $uploadedFile) : string
    {
        $directory = __DIR__.'/../public/upload/';
        $mime = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];
        $maxSize = 3000000;

        $file = $uploadedFile;
        $fileName = uniqid('upload-', TRUE).'.'.$file->getClientOriginalExtension();
        $fileMime = $file->getClientMimeType();
        $fileSize = $file->getSize();

        // Check mime type
        if(!in_array($fileMime, $mime))
        {
            dd('The file is not an image !');
            // Throw exception
        }

        // Check file size
        if($fileSize > $maxSize)
        {
            dd('The file size is too big !');
            // Throw exception
        }

        // Upload
        if($file->move($directory, $fileName))
        {
            // Return alert : Upload Succeed
        }
        else
        {
            dd('Upload Failed');
            // Throw exception
        }

        return $fileName;
    }
}