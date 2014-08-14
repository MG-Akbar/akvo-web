<?php
namespace JsonData\Admin\Model;

use JsonData\Config as JDConfig;
use JsonData\Admin\Form\Feed as FeedForm;
use JsonData\Common\Model\Dao\JsonData as JsonDataDao;
use JsonData\Common\Model\Feed as JsonDataFeed;
/**
 * Description of FormHandler
 *
 * @author Jayawi Perera
 */
class FormHandler {
    public function add () {

		$aContent = array();
        $aContent['redirect'] = '';
        $iId = false;
		$oDaoJsonData = new JsonDataDao();

		$oForm = new FeedForm(FeedForm::CONTEXT_CREATE);

		if (empty($_POST)) {

			$aPopulateData = array();
            $sDefaultMarkup = "<?php\r\n";
            $sDefaultMarkup .= "\t//feed data is contained in php array \$aData \r\n";
            $sDefaultMarkup .= "?> \r\n";
            $sDefaultMarkup .= "<ul class='jd_feed'> \r\n";
            $sDefaultMarkup .= "\t<?php \r\n";
            $sDefaultMarkup .= "\t\tforeach(\$aData AS \$key => \$value){ \r\n";
            $sDefaultMarkup .= "\t\t\t//do something \r\n";
            $sDefaultMarkup .= "\t\t\t?> \r\n";
            $sDefaultMarkup .= "\t\t\t<li><?php echo \$key; ?>: <?php echo \$value;?></li> \r\n";
            $sDefaultMarkup .= "\t\t\t<?php \r\n";
            $sDefaultMarkup .= "\t\t} \r\n";
            $sDefaultMarkup .= "\t?>\r\n";
            $sDefaultMarkup .= "</ul>\r\n";
            $aPopulateData['textTemplateMarkup'] = $sDefaultMarkup;

            $sDefaultStyle = "ul.jd_feed{\r\n";
            $sDefaultStyle .= "\tbackground: #c0c0c0; \r\n";
            $sDefaultStyle .= "} \r\n";
            $aPopulateData['textTemplateStylesheet'] = $sDefaultStyle;
            $oForm->populate($aPopulateData);

		} else {

			if ($oForm->isValid($_POST)) {

				$aFormValues = $oForm->getValues();

				$aInsertData = array();

				$aInsertData['feed_name'] = $aFormValues['textName'];
				$aInsertData['feed_slug'] = $aFormValues['hiddenSlug'];
				$aInsertData['feed_url'] = $aFormValues['textUrl'];
				if(is_array($_POST['textParam']))$aInsertData['feed_parameters'] = serialize($_POST['textParam']);
				$aInsertData['feed_update_interval'] = $aFormValues['selectUpdateInterval'];
//				$aInsertData['feed_template_markup'] = $aFormValues['textTemplateMarkup'];
//				$aInsertData['feed_template_css'] = $aFormValues['textTemplateStylesheet'];
				$aInsertData['date_created'] = date('Y-m-d H:i:s');
				$aInsertData['date_updated'] = date('Y-m-d H:i:s');
//				var_dump($aInsertData);


				$iId = $oDaoJsonData->insertFeed($aInsertData);
                $oFeed = new JsonDataFeed();
                $oFeed->updateCreateCache($iId,$aFormValues['textTemplateMarkup'],$aFormValues['textTemplateStylesheet']);
//				$oForm = new FeedForm(FeedForm::CONTEXT_CREATE, array('id' => 'iFormParticipantRegistryRegister'), array());
                
			} else {



			}

		}
        if ($iId != false) {
            $aContent['redirect'] = JDConfig::getHomeRedirectUrl();
        }else{
            $aContent['form'] = $oForm;
        }

		return $aContent;
	}

	public function edit () {

		$aContent = array();

		if (!isset($_GET['id'])) {

		}
        $aContent['redirect'] = '';
        $bStatus = false;
		$iId = $_GET['id'];

		$oDaoJsonData = new JsonDataDao();
		$aDetail = $oDaoJsonData->fetchFeed($iId);

		$oForm = new FeedForm(FeedForm::CONTEXT_UPDATE, array('id' => 'iFormRegistrantUpdate'), array('params'=>$aDetail['feed_parameters']));

		if (empty($_POST)) {

			$aPopulateData = array();

			$aPopulateData['textName'] = $aDetail['feed_name'];
			$aPopulateData['textSlug'] = $aDetail['feed_slug'];
			$aPopulateData['hiddenSlug'] = $aDetail['feed_slug'];
			$aPopulateData['textUrl'] = $aDetail['feed_url'];
			$aPopulateData['selectUpdateInterval'] = $aDetail['feed_update_interval'];
            $filename = JsonData_Plugin_Dir . '/cache/'.$iId.'/template.phtml';
            $handle = fopen($filename, "r");
            $contents = fread($handle, filesize($filename));
            fclose($handle);
			$aPopulateData['textTemplateMarkup']= $contents;
			$filename = JsonData_Plugin_Dir . '/cache/'.$iId.'/style.css';
            $handle = fopen($filename, "r");
            $contents = fread($handle, filesize($filename));
            fclose($handle);
			$aPopulateData['textTemplateStylesheet'] = $contents;




			$oForm->populate($aPopulateData);

		} else {

			if ($oForm->isValid($_POST)) {

				$aFormValues = $oForm->getValues();
				$aUpdateData = array();

				$aUpdateData['feed_name'] = $aFormValues['textName'];
				$aUpdateData['feed_slug'] = $aFormValues['hiddenSlug'];
				$aUpdateData['feed_url'] = $aFormValues['textUrl'];
                if(is_array($_POST['textParam']))$aUpdateData['feed_parameters'] = serialize($_POST['textParam']);
				$aUpdateData['feed_update_interval'] = $aFormValues['selectUpdateInterval'];
//				$aUpdateData['feed_template_markup'] = stripslashes($aFormValues['textTemplateMarkup']);
//				$aUpdateData['feed_template_css'] = stripslashes($aFormValues['textTemplateStylesheet']);
				$aUpdateData['date_updated'] = date('Y-m-d H:i:s');
//				var_dump($aInsertData);


				$bStatus = $oDaoJsonData->updateFeed($aUpdateData, $iId);
                $oFeed = new JsonDataFeed();
                $oFeed->updateCreateCache($iId,$aFormValues['textTemplateMarkup'],$aFormValues['textTemplateStylesheet']);
//				$oForm = new FeedForm(FeedForm::CONTEXT_CREATE, array('id' => 'iFormParticipantRegistryRegister'), array());

			} else {



			}

		}
        if ($bStatus != false) {
            $aContent['redirect'] = JDConfig::getHomeRedirectUrl();
        }else{
            $aContent['form'] = $oForm;
        }

		return $aContent;
	}

	public function remove () {

		$aContent = array();
		$aContent['redirect'] = '';

		if (!isset($_GET['id'])) {

		}

		$iId = $_GET['id'];

		$oDaoJsonData = new JsonDataDao();
		$aDetail = $oDaoJsonData->fetchFeed($iId);

		if (!empty($aDetail)) {
			$bStatus = $oDaoJsonData->deleteFeed($iId);

			if (is_int($bStatus)) {
				$aFeedQueues = $oDaoJsonData->fetchFeedQueue($iId); //get all feedques with same id

				$aToBeDeleteId = array();

				foreach ($aFeedQueues as $aFeedRaw) {
					$aToBeDeleteId[] = $aFeedRaw['id'];
				}

				array_map(array(&$oDaoJsonData,'deleteFeedQueue'),$aToBeDeleteId); //delete all feed associate with id

				$oFeed = new JsonDataFeed();
				$bStatus = $oFeed->removeFeedDir($iId); //remove files and directory

			}

		}

        if ($bStatus != false) {
            $aContent['redirect'] = JDConfig::getHomeRedirectUrl();
        }

		return $aContent;
	}

}