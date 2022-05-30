<?php
declare(strict_types=1);

namespace Flownative\WorkspacePreview\ViewHelpers;

use Flownative\TokenAuthentication\Security\Model\HashAndRoles;
use Flownative\TokenAuthentication\Security\Repository\HashAndRolesRepository;
use Neos\ContentRepository\Domain\Model\Workspace;
use Neos\FluidAdaptor\Core\Rendering\RenderingContext;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * A viewhelper to get a preview token by workspace
 */
class GetHashTokenForWorkspaceViewHelper extends AbstractViewHelper
{
    /**
     * Initializes the "then" and "else" arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('workspace', Workspace::class, 'The workspace to get a token for.', true);
    }

    /**
     * @return HashAndRoles|null
     */
    public function render(): ?HashAndRoles
    {
        return self::renderStatic($this->arguments, $this->buildRenderChildrenClosure(), $this->renderingContext);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return HashAndRoles|null
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): ?HashAndRoles
    {
        $workspace = $arguments['workspace'];
        $workspaceName = $workspace->getName();

        /** @var $renderingContext RenderingContext */
        $objectManager = $renderingContext->getObjectManager();
        $tokenRepository = $objectManager->get(HashAndRolesRepository::class);

        $tokens = $tokenRepository->findByRoles(['Flownative.WorkspacePreview:WorkspacePreviewer']);
        foreach ($tokens as $token) {
            $tokenWorkspaceName = $token->getSettings()['workspaceName'] ?? null;
            if ($tokenWorkspaceName === $workspaceName) {
                return $token;
            }
        }

        return null;
    }
}
