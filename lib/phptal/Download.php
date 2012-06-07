<?php
class PHPTAL_Php_Attribute_CHANGE_Download extends PHPTAL_Php_Attribute
{
	const LOCALE_PATH =  'm.media.download.';

	/**
     * Called before element printing.
     */
    public function before(PHPTAL_Php_CodeWriter $codewriter)
    {
		$expressions = $codewriter->splitExpression($this->expression);
		$media = 'null';
		$class = 'null';
		// foreach attribute
		foreach ($expressions as $exp)
		{
			list($attribute, $value) = $this->parseSetExpression($exp);
			switch ($attribute)
			{
				case 'class':
					$class = $codewriter->evaluateExpression($value);
					break;
				case 'document':
					$media = $codewriter->evaluateExpression($value);
					break;
				default:
					if ($media == 'null' && is_null($value) && !is_null($attribute))
					{
						$media = $codewriter->evaluateExpression($attribute);
					}
					break;
			}
		}
		$codewriter->doEchoRaw('PHPTAL_Php_Attribute_CHANGE_Download::render('.$media.', '.$class.')');
	}
	
    /**
     * Called after element printing.
     */
    public function after(PHPTAL_Php_CodeWriter $codewriter)
    {
	}
	
	/**
	 * @param media_persistentdocument_media $media
	 */
	public function getLang($media)
	{
		$lang = RequestContext::getInstance()->getLang();
		if ($media->isLangAvailable($lang))
		{
			return $lang;
		}
		return $media->getI18nInfo()->getVo();
	}

	/**
	 * @param media_persistentdocument_media $media
	 * @param String $lang
	 */
	public static function getContent($media, $lang)
	{
		$rc = RequestContext::getInstance();

		$rc->beginI18nWork($lang);

		if ($media->getTitle())
		{
		    $filename = $media->getTitle();
		}
		elseif ($media->getFilename())
		{
		    $filename = $media->getFilename();
		}
		else
		{
		    $filename = $media->getVoFilename();
		}

		$path = $media->getDocumentService()->getOriginalPath($media, true);
		$type = $media->getMediatype();
		
		// #7824 - intcours - display extension for "unknown" files :
		if (strtolower($type) == 'media')
		{
    		if ($media->getFilename())
    		{
    		    $type = f_util_FileUtils::getFileExtension($media->getFilename());
    		}
    		else
    		{
    		    $type = f_util_FileUtils::getFileExtension($media->getVoFilename());
    		}    		
		}

		$rc->endI18nWork();

		$langString = null;
		if ($lang !=  $rc->getLang())
		{
			$langString = f_Locale::translate( '&modules.uixul.bo.languages.' . ucfirst($lang) . ';' ). ', ';
		}
		
		$res = $filename . ' - ' . strtoupper($type) . ' (' . $langString;
		$res .= self::getFileSize($path);
		$res .= ')';
		return $res;
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public static function getFileSize($path)
	{
		if (is_readable($path))
		{
			$size = filesize($path);
			$i = 0;
			$iec = array("b", "kb", "mb", "gb", "tb", "pb", "eb", "zb", "yb");
			while ( ($size / 1024) > 1)
			{
				$size = $size / 1024;
				$i++;
			}
			$ls = LocaleService::getInstance();
			$res = sprintf("%.2f", $size).' <abbr title="'.$ls->transFO(self::LOCALE_PATH . strtolower($iec[$i]) .'-long', array('ucf','attr')).'">';
			$res .= $ls->transFO(self::LOCALE_PATH . strtolower($iec[$i]).'-acronym', array('ucf','attr'));
			$res .= '</abbr>';
			return $res;
		}
		return null;
	}

	/**
	 * @param media_persistentdocument_media $media
	 * @param String $class
	 * @return String
	 */
	public static function render($media, $class, $addcmpref = false)
	{
		if ($media === null)
		{
			return '';
		}
		$lang = self::getLang($media);
		$content = f_util_StringUtils::ucfirst(self::getContent($media, $lang));
		$title = LocaleService::getInstance()->transFO(self::LOCALE_PATH.'download', array('ucf', 'attr')) . ' ' . htmlspecialchars(strip_tags($content));
		$html = '<a';
		
		$attrs = array("href" => '"' . self::getUrl($media, $lang) . '"');
		if ($addcmpref)
		{
			$attrs['rel'] = '"cmpref:' . $media->getId() . '"';
		}

		if (f_util_StringUtils::isNotEmpty($class))
		{
			$attrs['class'] = '"link download ' . $class . '"';
		}
		else
		{
			$attrs['class'] = '"link download"';
		}
		
		$attrs['title'] = '"' . $title . '"';
		
		if (!$addcmpref)
		{
			$attrs = array_merge($attrs, self::getAdditionnalAttributes($media, $class));
		}
		
		foreach ($attrs as $attrName => $attrValue) 
		{
			$html .= ' ' . $attrName .'=' . $attrValue;
		}
		$html .= '>' . $content . "</a>";
		return $html;
	}
	
	private function getAdditionnalAttributes($media, $class)
	{
		$additionnalAttributes = array();
		foreach (MediaHelper::getAdditionnalDownloadAttributes($media, $class) as $attrName => $attrValue)
		{
			$additionnalAttributes[$attrName] = '"' .htmlspecialchars($attrValue, ENT_COMPAT, "UTF-8").'"';
		}
		return $additionnalAttributes;
	}

	/**
	 * @param media_persistentdocument_media $media
	 * @param string $lang
	 * @return string
	 */
	public static function getUrl($media, $lang)
	{
		if (!($media instanceof media_persistentdocument_file))
		{
			return '#';
		}
		return htmlspecialchars(media_FileService::getInstance()->generateDownloadUrl($media, $lang), ENT_COMPAT, "UTF-8");
	}
}