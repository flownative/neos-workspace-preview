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
        $forceConversationPathPart = 'forceConversion';

        if ($currentRenderingMode->isEdit() === false && $this->fusionValue($forceConversationPathPart) !== false) {
            $fullPath = $this->path . '/' . $forceConversationPathPart;
            $this->tsValueCache[$fullPath] = true;
        }

        return parent::evaluate();
    }

}
