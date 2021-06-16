<?php
namespace Flownative\WorkspacePreview\Fusion;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Neos\Domain\Service\ContentContext;
use Neos\Neos\Fusion\ConvertUrisImplementation as OriginalImplementation;

/**
 *
 */
class ConvertUrisImplementation extends OriginalImplementation
{
    /**
     * @return string
     * @throws \RuntimeException
     * @throws \Neos\Neos\Domain\Exception
     */
    public function evaluate()
    {
        $node = $this->fusionValue('node');
        if (!$node instanceof NodeInterface) {
            throw new \RuntimeException(sprintf('The current node must be an instance of NodeInterface, given: "%s".', gettype($node)), 1566317704);
        }

        /** @var ContentContext $contentContext */
        $contentContext = $node->getContext();
        if (!$contentContext instanceof ContentContext) {
            throw new \RuntimeException('Node did not have a ContentContext.', 1566317774);
        }

        $currentRenderingMode = $contentContext->getCurrentRenderingMode();
        $forceConversionPathPart = 'forceConversion';
        if ($currentRenderingMode->isEdit() === false && $this->fusionValue($forceConversionPathPart) !== false) {
            $fullPath = $this->path . '/' . $forceConversionPathPart;
            $this->tsValueCache[$fullPath] = true;
        }

        try {
            return parent::evaluate();
        } catch (\Throwable $e) {
        }
    }
}
