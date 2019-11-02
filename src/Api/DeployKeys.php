<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class DeployKeys extends ApiBase
{
    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('deploy_keys', $resolver->resolve($parameters));
    }
}