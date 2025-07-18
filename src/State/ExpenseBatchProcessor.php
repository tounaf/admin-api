<?php
// src/State/ExpenseBatchProcessor.php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\ExpenseBatchOutput;
use App\Entity\Expense;
use App\Entity\Fiangonana;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ExpenseBatchProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation->getUriTemplate() === '/expenses/batch') {
            // Gestion du batch POST
            if (!is_array($data->expenses)) {
                throw new BadRequestHttpException('Les données doivent être un tableau de dépenses.');
            }

            $createdExpenses = [];
            foreach ($data->expenses as $expenseData) {
                // Validation des champs requis
                if (!isset($expenseData['description'], $expenseData['amount'], $expenseData['date'], $expenseData['fiangonana'])) {
                    throw new BadRequestHttpException('Champs manquants dans une dépense : description, amount, date, fiangonana requis.');
                }

                $expense = new Expense();
                $expense->setDescription($expenseData['description']);
                $expense->setAmount((int)$expenseData['amount']);
                try {
                    $expense->setDateSabbat(new \DateTime($expenseData['date']));
                } catch (\Exception $e) {
                    throw new BadRequestHttpException('Format de date invalide : ' . $expenseData['date']);
                }

                // Résoudre l'IRI de fiangonana
                $fiangonanaIri = $expenseData['fiangonana'];
                if (!preg_match('#^/api/fiangonanas/(\d+)$#', $fiangonanaIri, $matches)) {
                    throw new BadRequestHttpException('IRI de fiangonana invalide : ' . $fiangonanaIri);
                }
                $fiangonanaId = (int)$matches[1];
                $fiangonana = $this->entityManager->getRepository(Fiangonana::class)->find($fiangonanaId);
                if (!$fiangonana) {
                    throw new BadRequestHttpException('Fiangonana non trouvé : ' . $fiangonanaIri);
                }
                $expense->setFiangonana($fiangonana);

                // Validation de l'entité
                $errors = $this->validator->validate($expense);
                if (count($errors) > 0) {
                    throw new BadRequestHttpException((string)$errors);
                }

                $this->entityManager->persist($expense);
                $this->entityManager->flush(); // Flush immédiat pour attribuer un ID
                $createdExpenses[] = $expense;
            }

            $json = $this->serializer->serialize(
                $createdExpenses,
                'json', // ou 'jsonld'
                ['groups' => ['read']]
            );

            return new JsonResponse($json, 201, [], true);


            //return new ExpenseBatchOutput($createdExpenses);

            // return $createdExpenses; // Retourne un tableau d'entités avec IDs
        } else {
            // Gestion du POST simple
            if (!isset($data->description, $data->amount, $data->date, $data->fiangonana)) {
                throw new BadRequestHttpException('Champs manquants : description, amount, date, fiangonana requis.');
            }

            $expense = new Expense();
            $expense->setDescription($data->description);
            $expense->setAmount((int)$data->amount);
            try {
                $expense->setDateSabbat(new \DateTime($data->date));
            } catch (\Exception $e) {
                throw new BadRequestHttpException('Format de date invalide : ' . $data->date);
            }

            // Résoudre l'IRI de fiangonana
            $fiangonanaIri = $data->fiangonana;
            if (!preg_match('#^/api/fiangonanas/(\d+)$#', $fiangonanaIri, $matches)) {
                throw new BadRequestHttpException('IRI de fiangonana invalide : ' . $fiangonanaIri);
            }
            $fiangonanaId = (int)$matches[1];
            $fiangonana = $this->entityManager->getRepository(Fiangonana::class)->find($fiangonanaId);
            if (!$fiangonana) {
                throw new BadRequestHttpException('Fiangonana non trouvé : ' . $fiangonanaIri);
            }
            $expense->setFiangonana($fiangonana);

            // Validation de l'entité
            $errors = $this->validator->validate($expense);
            if (count($errors) > 0) {
                throw new BadRequestHttpException((string)$errors);
            }

            $this->entityManager->persist($expense);
            $this->entityManager->flush();
            return $expense; // Retourne une seule entité
        }
    }
}