<?
use Bitrix\Main\Localization\Loc,
	Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class CIBlockPropertyFileMan
{
	const USER_TYPE = 'FileMan';

	public static function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE" => Iblock\PropertyTable::TYPE_STRING,
			"USER_TYPE" => self::USER_TYPE,
			"DESCRIPTION" => Loc::getMessage("IBLOCK_PROP_FILEMAN_DESC"),
			"GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
			"GetPropertyFieldHtmlMulty" => array(__CLASS__, "GetPropertyFieldHtmlMulty"),
			"ConvertToDB" => array(__CLASS__, "ConvertToDB"),
			"ConvertFromDB" => array(__CLASS__, "ConvertFromDB"),
			"GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
			'GetUIEntityEditorProperty' => array(__CLASS__, 'GetUIEntityEditorProperty'),
			'GetUIEntityEditorPropertyEditHtml' => array(__CLASS__, 'GetUIEntityEditorPropertyEditHtml'),
			'GetUIEntityEditorPropertyViewHtml' => array(__CLASS__, 'GetUIEntityEditorPropertyViewHtml'),
		);
	}

	public static function GetPropertyFieldHtmlMulty($arProperty, $arValues, $strHTMLControlName)
	{
		if($strHTMLControlName["MODE"]=="FORM_FILL" && CModule::IncludeModule('fileman'))
		{
			$inputName = array();
			$description = array();
			foreach ($arValues as $intPropertyValueID => $arOneValue)
			{
				$key = $strHTMLControlName["VALUE"]."[".$intPropertyValueID."]";
				$inputName[$key."[VALUE]"] = $arOneValue["VALUE"];
				$description[$key."[DESCRIPTION]"] = $arOneValue["DESCRIPTION"];
			}

			return CFileInput::ShowMultiple($inputName, $strHTMLControlName["VALUE"]."[n#IND#][VALUE]", array(
				"PATH" => "Y",
				"IMAGE" => "N",
				"MAX_SIZE" => array(
					"W" => COption::GetOptionString("iblock", "detail_image_size"),
					"H" => COption::GetOptionString("iblock", "detail_image_size"),
				),
			), false, array(
				'upload' => false,
				'medialib' => true,
				'file_dialog' => true,
				'cloud' => true,
				'del' => true,
				'description' => $arProperty["WITH_DESCRIPTION"]=="Y"? array(
					"VALUES" => $description,
					'NAME_TEMPLATE' => $strHTMLControlName["VALUE"]."[n#IND#][DESCRIPTION]",
				): false,
			));
		}
		else
		{
			$table_id = md5($strHTMLControlName["VALUE"]);
			$return = '<table id="tb'.$table_id.'" border=0 cellpadding=0 cellspacing=0>';
			foreach ($arValues as $intPropertyValueID => $arOneValue)
			{
				$return .= '<tr><td>';

				$return .= '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]."[$intPropertyValueID][VALUE]").'" size="'.$arProperty["COL_COUNT"].'" value="'.htmlspecialcharsEx($arOneValue["VALUE"]).'">';

				if (($arProperty["WITH_DESCRIPTION"]=="Y") && ('' != trim($strHTMLControlName["DESCRIPTION"])))
					$return .= ' <span title="'.Loc::getMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_TITLE").'">'.Loc::getMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_LABEL").':<input name="'.htmlspecialcharsEx($strHTMLControlName["DESCRIPTION"]."[$intPropertyValueID][DESCRIPTION]").'" value="'.htmlspecialcharsEx($arOneValue["DESCRIPTION"]).'" size="18" type="text"></span>';

				$return .= '</td></tr>';
			}

			$return .= '<tr><td>';
			$return .= '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]."[n0][VALUE]").'" size="'.$arProperty["COL_COUNT"].'" value="">';
			if (($arProperty["WITH_DESCRIPTION"]=="Y") && ('' != trim($strHTMLControlName["DESCRIPTION"])))
				$return .= ' <span title="'.Loc::getMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_TITLE").'">'.Loc::getMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_LABEL").':<input name="'.htmlspecialcharsEx($strHTMLControlName["DESCRIPTION"]."[n0][DESCRIPTION]").'" value="" size="18" type="text"></span>';
			$return .= '</td></tr>';

			$return .= '<tr><td><input type="button" value="'.Loc::getMessage("IBLOCK_PROP_FILEMAN_ADD").'" onClick="BX.IBlock.Tools.addNewRow(\'tb'.$table_id.'\')"></td></tr>';
			return $return.'</table>';
		}
	}

	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		global $APPLICATION;

		if (trim($strHTMLControlName["FORM_NAME"]) == '')
			$strHTMLControlName["FORM_NAME"] = "form_element";
		$name = preg_replace("/[^a-zA-Z0-9_]/i", "x", htmlspecialcharsbx($strHTMLControlName["VALUE"]));

		if(is_array($value["VALUE"]))
		{
			$value["VALUE"] = $value["VALUE"]["VALUE"];
			$value["DESCRIPTION"] = $value["DESCRIPTION"]["VALUE"];
		}

		if($strHTMLControlName["MODE"]=="FORM_FILL" && CModule::IncludeModule('fileman'))
		{
			return CFileInput::Show($strHTMLControlName["VALUE"], $value["VALUE"],
				array(
					"PATH" => "Y",
					"IMAGE" => "N",
					"MAX_SIZE" => array(
						"W" => COption::GetOptionString("iblock", "detail_image_size"),
						"H" => COption::GetOptionString("iblock", "detail_image_size"),
					),
				), array(
					'upload' => false,
					'medialib' => true,
					'file_dialog' => true,
					'cloud' => true,
					'del' => true,
					'description' => $arProperty["WITH_DESCRIPTION"]=="Y"? array(
						"VALUE" => $value["DESCRIPTION"],
						"NAME" => $strHTMLControlName["DESCRIPTION"],
					): false,
				)
			);
		}
		else
		{
			$return = '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" id="'.$name.'" size="'.$arProperty["COL_COUNT"].'" value="'.htmlspecialcharsEx($value["VALUE"]).'">';

			if (($arProperty["WITH_DESCRIPTION"]=="Y") && ('' != trim($strHTMLControlName["DESCRIPTION"])))
			{
				$return .= ' <span title="'.Loc::getMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_TITLE").'">'.Loc::getMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_LABEL").':<input name="'.htmlspecialcharsEx($strHTMLControlName["DESCRIPTION"]).'" value="'.htmlspecialcharsEx($value["DESCRIPTION"]).'" size="18" type="text"></span>';
			}

			return $return;
		}
	}

	public static function ConvertToDB($arProperty, $value)
	{
		$result = array();
		$return = array();
		if(is_array($value["VALUE"]))
		{
			$result["VALUE"] = $value["VALUE"]["VALUE"];
			$result["DESCRIPTION"] = $value["DESCRIPTION"]["VALUE"];
		}
		else
		{
			$result["VALUE"] = $value["VALUE"];
			$result["DESCRIPTION"] = $value["DESCRIPTION"];
		}
		$return["VALUE"] = trim($result["VALUE"]);
		$return["DESCRIPTION"] = trim($result["DESCRIPTION"]);
		return $return;
	}

	public static function ConvertFromDB($arProperty, $value)
	{
		$return = array();
		if (trim($value["VALUE"]) <> '')
			$return["VALUE"] = $value["VALUE"];
		if (trim($value["DESCRIPTION"]) <> '')
			$return["DESCRIPTION"] = $value["DESCRIPTION"];
		return $return;
	}

	public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		$arPropertyFields = array(
			"HIDE" => array("MULTIPLE_CNT"),
		);

		return '';
	}

	public static function GetUIEntityEditorProperty($settings, $value)
	{
		return [
			'type' => 'custom'
		];
	}

	public static function GetUIEntityEditorPropertyEditHtml(array $params = []) : string
	{
		$settings = $params['SETTINGS'] ?? [];
		$value = $params['VALUE'] ?? '';
		$paramsHTMLControl = [
			'MODE' => 'iblock_element_admin',
			'VALUE' => $params['FIELD_NAME'] ?? '',
		];
		return self::GetPropertyFieldHtml($settings, $value, $paramsHTMLControl);
	}

	public static function GetUIEntityEditorPropertyViewHtml(array $params = []) : string
	{
		$result = '';
		if(!empty($params['VALUE']))
		{
		}
		return $result;
	}
}