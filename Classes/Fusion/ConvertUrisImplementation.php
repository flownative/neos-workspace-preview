<?php
namespace Flownative\WorkspacePreview\Fusion;

use Neos\Neos\Domain\Service\UserInterfaceModeService;
use Neos\Neos\Fusion\ConvertUrisImplementation as OriginalImplementation;
use Neos\Flow\Annotations as Flow;

class ConvertUrisImplementation extends OriginalImplementation
{

    /**
     * @Flow\Inject
     * @var UserInterfaceModeService
     */
    protected $interfaceRenderModeService;

    /**
     * @return string
     * @throws \Neos\Neos\Domain\Exception
     */
    public function evaluate()
    {
        $currentRenderingMode = $this->interfaceRenderModeService->findModeByCurrentUser();
        $forceConversionPathPart = 'forceConversion';

        if ($currentRenderingMode->isEdit() === false && $this->fusionValue($forceConversionPathPart) !== false) {
            $fullPath = $this->path . '/' . $forceConversionPathPart;
            $this->tsValueCache[$fullPath] = true;
        }

        return parent::evaluate();
    }

}
