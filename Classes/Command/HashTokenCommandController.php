<?php
namespace Flownative\WorkspacePreview\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Flownative\WorkspacePreview\WorkspacePreviewTokenFactory;

/**
 *
 */
class HashTokenCommandController extends CommandController
{
    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject
     * @var WorkspacePreviewTokenFactory
     */
    protected $workspacePreviewTokenFactory;

    /**
     * Create a token for previewing the workspace with the given name/identifier.
     *
     * @param string $workspaceName
     */
    public function createWorkspacePreviewTokenCommand(string $workspaceName)
    {
        $tokenmetadata = $this->workspacePreviewTokenFactory->create($workspaceName);
        $this->persistenceManager->persistAll();

        $this->outputLine('Created token with hash "%s"', [$tokenmetadata->getHash()]);
    }
}
