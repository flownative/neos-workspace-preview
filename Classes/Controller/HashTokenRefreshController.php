<?php
namespace Flownative\WorkspacePreview\Controller;

use Neos\Error\Messages\Message;
use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\Workspace;
use Neos\Flow\Mvc\Controller\ActionController;
use Flownative\WorkspacePreview\WorkspacePreviewTokenFactory;

/**
 * Service controller to refresh a preview token for a given workspace.
 */
class HashTokenRefreshController extends ActionController
{
    /**
     * @Flow\Inject
     * @var WorkspacePreviewTokenFactory
     */
    protected $workspacePreviewTokenFactory;

    /**
     * Will refresh the hash or create an entirely new token for the given workspace
     *
     * @param Workspace $workspace
     */
    public function refreshHashTokenForWorkspaceAction(Workspace $workspace): void
    {
        $this->workspacePreviewTokenFactory->refresh($workspace->getName());
        $this->addFlashMessage('A new preview token has been generated for workspace "%s", the old one is invalid now!', '', Message::SEVERITY_OK, [$workspace->getTitle()]);
        $this->redirectToRequest($this->request->getReferringRequest());
    }
}
