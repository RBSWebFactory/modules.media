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
        $format = $request->getParameter('format');   
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
        	$HTTP_Header= new HTTP_Header();
			$HTTP_Header->sendStatusCode(404);
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