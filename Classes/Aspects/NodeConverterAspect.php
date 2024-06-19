<?php
namespace Flownative\WorkspacePreview\Aspects;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Flow\Security\Context;
use Neos\Neos\Domain\Service\UserInterfaceModeService;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class NodeConverterAspect
{

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;

    /**
     * @Flow\Inject
     * @var UserInterfaceModeService
     */
    protected $userInterfaceModeService;

    /**
     * @Flow\Around("method(Neos\ContentRepository\TypeConverter\NodeConverter->prepareContextProperties())")
     * @param \Neos\Flow\Aop\JoinPointInterface $joinPoint The current join point
     * @return array
     */
    public function suppressInvisibleContentShown(JoinPointInterface $joinPoint)
    {
        $contextProperties = $joinPoint->getAdviceChain()->proceed($joinPoint);
        $workspaceName = $joinPoint->getMethodArgument('workspaceName');

        try {
            if (
                $workspaceName !== 'live'
                && (
                    $this->userInterfaceModeService->findModeByCurrentUser()->isPreview()
                    || $this->securityContext->hasRole('Flownative.WorkspacePreview:WorkspacePreviewer')
                )
            ) {
                $contextProperties['invisibleContentShown'] = false;
            }
        } catch (\Neos\Flow\Security\Exception\NoSuchRoleException|\Neos\Flow\Security\Exception $e) {
            // ignore
        }

        return $contextProperties;
    }
}
