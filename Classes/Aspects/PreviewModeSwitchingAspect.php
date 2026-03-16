<?php

namespace Flownative\WorkspacePreview\Aspects;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\Security\Context;
use Neos\Neos\Domain\Service\UserInterfaceModeService;
use Neos\Neos\Domain\Service\UserService;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class PreviewModeSwitchingAspect
{
    const PREVIOUS_MODE_KEY = 'flownative.workspacePreview.previousEditPreviewMode';

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
     * @FLow\Inject
     * @var UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\InjectConfiguration()
     * @var array
     */
    protected array $settings;


    /**
     * @Flow\Around("method(Neos\Neos\Ui\Controller\BackendController->indexAction())")
     * @param \Neos\Flow\Aop\JoinPointInterface $joinPoint The current join point
     */
    public function backendEntryJoinPoint(JoinPointInterface $joinPoint)
    {
        $this->switchPreviewMode($joinPoint);
        return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }


    /**
     * @Flow\Around("method(Neos\Neos\Controller\Frontend\NodeController->previewAction())")
     * @param \Neos\Flow\Aop\JoinPointInterface $joinPoint The current join point
     */
    public function previewRouteJoinPoint(JoinPointInterface $joinPoint)
    {
        $this->switchPreviewMode($joinPoint);
        return $joinPoint->getAdviceChain()->proceed($joinPoint);

    }


    /**
     * @param JoinPointInterface $joinPoint
     * @return void
     */
    public function switchPreviewMode(JoinPointInterface $joinPoint): void
    {
        if ($this->settings['userInterfaceModeSwitcher']['enabled'] !== true) {
            return;
        }

        $methodArguments = $joinPoint->getMethodArguments();
        if (!$methodArguments['node'] instanceof NodeInterface) {
            return;
        }

        /** @var NodeInterface $node */
        $node = $methodArguments['node'];

        $user = $this->userService->getCurrentUser();

        if ($user !== null) {
            $userPreferences = $user->getPreferences()->getPreferences();

            if (str_starts_with($node->getContext()->getWorkspaceName(), 'user-')) {
                //Reset to previous editPreviewMode
                if (isset($userPreferences[self::PREVIOUS_MODE_KEY])) {
                    $userPreferences['contentEditing.editPreviewMode'] = $userPreferences[self::PREVIOUS_MODE_KEY];
                    unset($userPreferences[self::PREVIOUS_MODE_KEY]);
                }
            } elseif (!isset($userPreferences[self::PREVIOUS_MODE_KEY])) {
                //Set editPreviewMode
                $userPreferences[self::PREVIOUS_MODE_KEY] = $userPreferences['contentEditing.editPreviewMode'] ?: $this->userInterfaceModeService->findDefaultMode()->getName();
                $userPreferences['contentEditing.editPreviewMode'] = $this->settings['userInterfaceModeSwitcher']['previewMode'];
            } else {
                return;
            }

            $newUserPreferences = clone $user->getPreferences();
            $newUserPreferences->setPreferences($userPreferences);

            $user->setPreferences($newUserPreferences);
            $this->userService->updateUser($user);
            $this->persistenceManager->persistAll();
        }
    }
}
