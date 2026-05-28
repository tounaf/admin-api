<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\SabbatValidation;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SabbatValidationProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        #[Autowire('%kernel.project_dir%/public/uploads')] 
        private string $baseUploadDir
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof SabbatValidation && $operation instanceof Post) {
            
            $base64Image = $data->getImageName(); 
            $fiangonana = $data->getFiangonana();

            if ($fiangonana && $base64Image && preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
                
                $extension = strtolower($matches[1]);
                $base64Data = substr($base64Image, strpos($base64Image, ',') + 1);
                $binaryData = base64_decode($base64Data);

                // Construction du chemin dynamique : uploads / {id} / bordereau
                $relativeDir = '/' . $fiangonana->getId() . '/bordereau';
                $fullDir = $this->baseUploadDir . $relativeDir;

                // Création récursive du dossier (le 'true' permet de créer toute l'arborescence)
                if (!file_exists($fullDir)) {
                    mkdir($fullDir, 0777, true);
                }

                $fileName = uniqid('BRD_') . '.' . $extension;
                $filePath = $fullDir . '/' . $fileName;
                
                if (file_put_contents($filePath, $binaryData) !== false) {
                    // On enregistre le chemin relatif dans la base de données 
                    // pour pouvoir reconstruire l'URL facilement plus tard
                    $data->setImageName($relativeDir . '/' . $fileName);
                    $data->setStatus('PENDING'); 
                }
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}