<?php
/**
 * @package modules.media
 */
class media_DisplayMediaDescriptionAction extends media_DisplayAction
{
	/**
	 * @param change_Context $context
	 * @param change_Request $request
	 */
	protected function displayMedia ($media, $context, $request)
	{
		$this->setContentType("text/html");
		$template = TemplateLoader::getInstance()->setPackageName("modules_media")->setDirectory("templates")->load("Media-MediaDescription");
		$template->setAttribute("media", $media);
		$website = website_WebsiteService::getInstance()->getCurrentWebsite();
		$template->setAttribute("website", $website);
		echo $template->execute();
		return change_View::NONE;
	}
}