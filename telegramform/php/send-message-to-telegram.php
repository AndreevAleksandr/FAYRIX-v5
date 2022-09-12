<?php 

	const TOKEN = '5449831040:AAGusOjYwE5ajxfQAw-uilZDow3e_UXGv2o';
	const CHATID = '-785868249';

	$types = array('application/pdf');

	$size = 1073741824;

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$fileSendStatus = '';
		$textSendStatus = '';
		$msgs = [];

		if (!empty($_POST['email']) && !empty($_POST['privacy_policy'])) {

			$txt = "";

			if (isset($_POST['position']) && !empty($_POST['position'])) {
				$txt .= " Position: " . strip_tags(trim(urlencode($_POST['position'])));
			}
			if (isset($_POST['grade']) && !empty($_POST['grade'])) {
				$txt .= " Grade: " . strip_tags(trim(urlencode($_POST['grade'])));
			}
			if (isset($_POST['lvl_english']) && !empty($_POST['lvl_english'])) {
				$txt .= " LVL English: " . strip_tags(trim(urlencode($_POST['lvl_english'])));
			}
			if (isset($_POST['email']) && !empty($_POST['email'])) {s
				$txt .= " E-Mail: " . strip_tags(trim(urlencode($_POST['email']))) . "%0A";
			}
			if (isset($_POST['link']) && !empty($_POST['link'])) {
				$txt .= " Link: " . strip_tags(trim(urlencode($_POST['link']))) . "%0A";
			}
			if (isset($_POST['comment']) && !empty($_POST['comment'])) {
				$txt .= " Comment: " . strip_tags(trim(urlencode($_POST['comment']))) . "%0A";
			}
			if (isset($_POST['test_task']) && !empty($_POST['test_task'])) {
				$txt .= " Test Task: " . strip_tags(trim(urlencode($_POST['test_task'])));
			}
			if (isset($_POST['privacy_policy']) && !empty($_POST['privacy_policy'])) {
				$txt .= " Privacy policy: " . strip_tags(trim(urlencode($_POST['privacy_policy'])));
			}

			$textSendStatus = @file_get_contents('https://api.telegram.org/bot'. TOKEN . '/sendMessage?chat_id=' . CHATID . '&parse_mode=html&text=' . $txt);

			if (isset(json_decode($textSendStatus)->{'ok'}) && json_decode($textSendStatus) ->{'ok'}) {
				if (!empty($_FILES['files']['tmp_name'])) {
					$urlFile = "https://api.telegram.org/bot" . TOKEN . "/sendMediaGroup";

					$path = $_SERVER['DOCUMENT_ROOT'] . '/telegramform/tmp/';

					$mediaData = [];
					$postContent = [
						'chat_id' => CHATID,
					];

					for ($ct = 0; $ct < count($_FILES['files']['tmp_name']); $ct++) {
						if ($_FILES['files']['name'][$ct] && @copy($_FILES['files']['tmp_name'][$ct], $path . $_FILES['files']['name'][$ct])) {
							if ($_FILES['files']['size'][$ct] < $size && in_array($_FILES['files']['type'][$ct], $types)) {
								$filePath = $path . $_FILES['files']['name'][$ct];
								$postContent[$_FILES['files']['name'][$ct]] = new CURLFile(realpath($filePath));
								$mediaData[] = ['type' => 'document', 'media' => 'attach://' . $_FILES['files']['name'][$ct]];
							}
						}
					}

					$postContent['media'] = json_encode($mediaData);

					$curl = curl_init();
					curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
					curl_setopt($curl, CURLOPT_URL, $urlFile);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $postContent);
					$fileSendStatus = curl_exec($curl);
					curl_close($curl);
					$files = glob($path.'*');
					foreach($files as $file){
						if (is_file($file)) 
							unlink($file);
					}
				}
				echo json_encode('SUCCESS');
			}else {
				echo json_encode('ERROR');
				//
				// echo json_decode($textSendStatus);
			}	

		}else {
			echo json_encode('NOTVALID');
		}
	}else {
		header("Location: /");
	}