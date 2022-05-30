<?php
declare(strict_types=1);

namespace Flownative\WorkspacePreview\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Neos\Domain\Exception as DomainException;
use Neos\Neos\Domain\Service\UserInterfaceModeService;
use Neos\Neos\Fusion\ConvertUrisImplementation as OriginalImplementation;

class ConvertUrisImplementation extends OriginalImplementation
{

    /**
     * @Flow\Inject
     * @var UserInterfaceModeService
     */
    protected $interfaceRenderModeService;

    /**
     * @return string
     * @throws DomainException
     */
    public function evaluate(): string
    {
        $currentRenderingMode = $this->interfaceRenderModeService->findModeByCurrentUser();
        $forceConversionPathPart = 'forceConversion';

        if ($currentRenderingMode->isEdit() === false && $this->fusionValue($forceConversionPathPart) !== false) {
            $fullPath = $this->path . '/' . $forceConversionPathPart;
            $this->fusionValueCache[$fullPath] = true;
        }

        return parent::evaluate();
    }

}
