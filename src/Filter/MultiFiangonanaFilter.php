<?php
namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class MultiFiangonanaFilter extends AbstractFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ($property !== 'fiangonana' || !is_array($value)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName('fiangonana');

        // Convertir les IRIs en IDs
        $ids = array_map(function ($iri) {
            return basename($iri); // Extrait l'ID de l'IRI (ex: /api/fiangonanas/1 -> 1)
        }, $value);

        $queryBuilder
            ->andWhere(sprintf('%s.fiangonana IN (:%s)', $rootAlias, $parameterName))
            ->setParameter($parameterName, $ids);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'fiangonana[]' => [
                'property' => 'fiangonana',
                'type' => Type::BUILTIN_TYPE_ARRAY,
                'required' => false,
                'description' => 'Filtrer par plusieurs Fiangonana (IRIs)',
                'openapi' => [
                    'example' => ['/api/fiangonanas/1', '/api/fiangonanas/2'],
                    'allowEmptyValue' => true,
                    'explode' => true,
                ],
            ],
        ];
    }
}