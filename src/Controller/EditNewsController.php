<?php

declare(strict_types=1);

namespace Seminario\Mvc\Controller;

use Seminario\Mvc\Entity\News;
use Seminario\Mvc\Helper\FlashMessageTrait;
use Seminario\Mvc\Repository\NewsRepository;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EditNewsController implements RequestHandlerInterface
{
    use FlashMessageTrait;

    public function __construct(private NewsRepository $newsRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $id = filter_var($queryParams['id'], FILTER_VALIDATE_INT);
        if ($id === false || $id === null) {
            $this->addErrorMessage('ID inválido');
            return new Response(302, [
                'Location' => '/'
            ]);
        }

        $requestBody = $request->getParsedBody();
        $content = filter_var($requestBody['content'], FILTER_VALIDATE_URL);
        if ($content === false) {
            $this->addErrorMessage('Conteúdo inválido');
            return new Response(302, [
                'Location' => '/'
            ]);
        }
        $title = filter_var($requestBody['title']);
        if ($title === false) {
            $this->addErrorMessage('Título não informado');
            return new Response(302, [
                'Location' => '/'
            ]);
        }

        $author = filter_var($requestBody['author']);
        if ($author === false) {
            $this->addErrorMessage('Autor não informado');
            return new Response(302, [
                'Location' => '/'
            ]);
        }

        $News = new News($title, $content, $author, new \DateTime());
        $News->setId($id);

        $success = $this->newsRepository->update($News);

        if ($success === false) {
            $this->addErrorMessage('Erro ao atualizar o notícia');
            return new Response(302, [
                'Location' => '/'
            ]);
        }

        return new Response(302, [
            'Location' => '/'
        ]);
    }
}