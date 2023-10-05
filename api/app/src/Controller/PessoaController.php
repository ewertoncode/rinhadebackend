<?php 
namespace App\Controller;

use JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PessoaController extends AbstractController
{
    #[Route('/pessoas', methods: 'POST', name: 'app_cria_pessoa')]
    public function criaPessoa(Request $request) : JsonResponse
    {
        try {
            $payload = $request->getPayload();
            
            if(empty($payload->get('apelido')) or !is_string($payload->get('apelido')) or strlen($payload->get('apelido')) > 32) {
                return $this->json(['mensagem' => 'Apelido inválido'], 422);
            }

            //return $this->json(['mensagem' => 'Criado com sucesso'], 201, ['Location' => '/pessoas/123']);
            header('Location: /pessoas/123', true, 201);
            exit;
        } catch(JsonException $jsonException) {
            return $this->json(['mensagem' => 'Payload não é um Json.'], 400);
        }
    }

    #[Route('/pessoas/{id}', name: 'app_consulta_pessoa')]
    public function consutaPessoaById(string $id): JsonResponse
    {
        return $this->json([
            'id' => $id,
            'apelido' => 'José',
            'nome' => 'José Maria',
            'nascimento' => '2000-10-01',
            'stack' => ["C#", "Node", "Oracle"]
        ]);
    }
}