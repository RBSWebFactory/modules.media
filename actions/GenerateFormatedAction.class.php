<?php
/**
 * @package modules.media
 */
class media_GenerateFormatedAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
    {
    	$matches = null;
    	if (isset($_SERVER["REDIRECT_URL"]) && preg_match('/^\/publicmedia\/formatted\/(.+)$/', $_SERVER["REDIRECT_URL"], $matches))
    	{
    		$format = $matches[1];
    	}
    	else
    	{
    		$format = $request->getParameter('format');	
    	}
    	   
        try 
        {   
        	if (Framework::isInfoEnabled())
        	{
        		Framework::info(__METHOD__ . ':' . $format);
        	}
        	media_FormatterHelper::outputFormatedMedia($format);
        }
        catch (Exception $e)
        {
        	Framework::exception($e);
        	f_web_http_Header::setStatus(404);
        }
        return View::NONE;
    }

    public function isSecure()
    {
    	return false;
    }

    public function getRequestMethods ()
    {
        return Request::GET;
    }
}