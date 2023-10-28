<?php

declare(strict_types=1);

namespace RLTSquare\IndexerReindex\Controller\Adminhtml\Indexer;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Indexer\Model\Indexer\Collection;
use Magento\Indexer\Model\IndexerFactory;

/**
 * Class IndexerReindex for Reindexing
 */
class IndexerReindex extends Action
{
    /**
     * @var IndexerFactory
     */
    private IndexerFactory $indexer;

    private $indexCollection;

    /**
     * @param Context $context
     * @param IndexerFactory $indexer
     */
    public function __construct(
        Context        $context,
        IndexerFactory $indexer,
        Collection     $indexCollection
    ) {
        $this->indexer = $indexer;
        $this->indexCollection = $indexCollection;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        try {
            $indexes = $this->indexCollection->getAllIds();
            foreach ($indexes as $index) {
                $indexFactory = $this->indexer->create()->load($index);
                $indexFactory->reindexAll($index);
                $indexFactory->reindexRow($index);
            }
            $this->getMessageManager()->addSuccessMessage(__('Reindexing completed successfully.'));
        } catch (Exception $e) {
            $this->getMessageManager()->addErrorMessage(
                __('Error in Reindexing!')
            );
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('indexerreindex/indexer/index');
    }
}
