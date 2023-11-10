<?php 
namespace App\Controller;

use App\Entity\Pessoa;
use App\Repository\PessoaRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PessoaController extends AbstractController
{
    #[Route('/pessoas', methods: 'POST', name: 'app_cria_pessoa')]
    public function criaPessoa(Request $request, EntityManagerInterface $em) : JsonResponse
    {

        //header("Location: /pessoas/123", true, 201);
        //    exit;
            

        try {
            $payload = $request->toArray();

            $data = new \DateTime($payload['nascimento']);
            
            if(
                empty($payload['apelido']) or 
                !is_string($payload['apelido']) or 
                !is_string($payload['nome']) or 
                strlen($payload['apelido']) > 32 or 
                !$this->is_array_strings($payload['stack'])
                ) {
                return $this->json(['mensagem' => 'Dados inválidos inválido'], 422);
            }
            

            $pessoa = new Pessoa();
            $pessoa->setApelido($payload['apelido'])
                ->setNome($payload['nome'])
                ->setNascimento(new \DateTime($payload['nascimento']))
                ->setStack(is_array($payload['stack']) ? $payload['stack'] : null);
               

                $em->persist($pessoa);
                $em->flush();



            //return $this->json(['mensagem' => 'Criado com sucesso'], 201, ['Location' => "/pessoas/{$pessoa->getId()}"]);
            header("Location: /pessoas/{$pessoa->getId()}", true, 201);
            exit;
        } 
        catch(UniqueConstraintViolationException $unqueException) {
            return $this->json(['mensagem' => "Apelido Já existe."], 422);
        }
        catch(JsonException $jsonException) {
            return $this->json(['mensagem' => 'Payload não é um Json.'], 400);
        }
        catch(Exception $e) {
            return $this->json(['mensagem' => 'Payload inválido.'], 422);
        }
    }

    #[Route('/pessoas/{id}', name: 'app_consulta_pessoa')]
    public function consutaPessoaById(string $id, PessoaRepository $pessoaRepository): JsonResponse
    {
       // return $this->json([], 200);
        try {
            $pessoa = $pessoaRepository->find($id);
            if($pessoa == null) throw new Exception("Pessoa não existe");
            return $this->json(['id' => $pessoa->getId()], 200);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], 404);
        }
    }

    #[Route('/contagem-pessoas', name: 'app_consulta_pessoa_count')]
    public function contagemPessoas( PessoaRepository $pessoaRepository): JsonResponse
    {
        $pessoas = $pessoaRepository->findAll();
        return $this->json(['total' => count($pessoas)], 200);
    }

    private function is_array_strings($arr): bool
    {
        if(empty($arr)) return true;

        foreach($arr as $value) {
            if(!is_string($value)) return false;
        }

        return true;
    }
}