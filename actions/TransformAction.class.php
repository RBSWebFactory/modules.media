<?php
class media_TransformAction extends change_JSONAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	public function _execute($context, $request)
	{
		$way = $request->getParameter('way');
		$documents= $this->getDocumentInstanceArrayFromRequest($request);
		switch ($way)
		{
			case "toSecureMedia":
				$this->transformToSecureMedia($documents);
				break;
						
			case "toMedia":
				$this->transformToMedia($documents);
				break;
			default:
				throw new Exception('Invalid way parameter');
				break;
		}
		return $this->sendJSON(array('message' => 
		LocaleService::getInstance()->trans('m.media.bo.actions.transform' . strtolower($way). '-success')));
	}
	
	/**
	 * @param Array<media_persistentdocument_media> $documents
	 */	
	public function transformToSecureMedia($documents)
	{
		$documentService  = media_MediaService::getInstance(); 	
		foreach ($documents as $document)
		{
			if ($document->getDocumentModelname() == "modules_media/media")
			{
				$documentService->transform($document, "modules_media/securemedia");
				$this->logAction($document);
			}
		}
	}
	
	/**
	 * @param Array<media_persistentdocument_securemedia> $documents
	 */	
	public function transformToMedia($documents)
	{
		$documentService  = media_MediaService::getInstance(); 	
		foreach ($documents as $document)
		{
			if ($document->getDocumentModelname() == "modules_media/securemedia")
			{
				$documentService->transform($document, "modules_media/media");
				$this->logAction($document);
			}
		}
	}	
	
	/**
	 * @return boolean true
	 */
	protected function isDocumentAction()
	{
		return true;
	}
}