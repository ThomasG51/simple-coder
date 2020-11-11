<?php

namespace Lib;


use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;


abstract class AbstractController
{
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
            'cache' => false
        ]);

        $twig->addFunction(new TwigFunction('asset', function ($asset) {
            return sprintf('../assets/%s', ltrim($asset, '/'));
        }));

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
     * @param array $file
     * @param string $coverName
     */
    public function uploadFile(array $file, string $coverName) : void
    {
        $directory = __DIR__.'/../public/upload/';
        $mime = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];
        $maxSize = 3000000;

        $fileName = $coverName;
        $fileTmpName = $file['tmp_name'];
        $fileMime = mime_content_type($fileTmpName);
        $fileSize = filesize($fileTmpName);

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
        if(move_uploaded_file($fileTmpName, $directory.$fileName))
        {
            // Return alert : Upload Succeed
        }
        else
        {
            dd('Upload Failed');
            // Throw exception
        }
    }
}