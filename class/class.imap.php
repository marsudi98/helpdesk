<?php

/**
 * Helper class for imap access
 *
 * @package    protocols
 * @copyright  Copyright (c) Tobias Zeising (http://www.aditu.de)
 * @license    GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)
 * @author     Tobias Zeising <tobias.zeising@aditu.de>
 * @modified   JAKWEB
 */
class Imap {
    
    /**
     * imap connection
     */
    protected $imap = false;
    
    /**
     * mailbox url string
     */
    protected $mailbox = "";
    
    /**
     * currentfolder
     */
    protected $folder = "Inbox";
    
    /**
     * initialize imap helper
     *
     * @return void
     * @param $mailbox imap_open string
     * @param $username
     * @param $password
     * @param $encryption ssl or tls
     */
    public function __construct($mailbox, $username, $password, $encryption = false) {
        $enc = '';
        if($encryption!=null && isset($encryption) && $encryption=='ssl')
            $enc = '/imap/ssl/novalidate-cert';
        else if($encryption!=null && isset($encryption) && $encryption=='tls')
            $enc = '/imap/tls/novalidate-cert';
        $this->mailbox = "{" . $mailbox . $enc . "}";
        $this->imap = @imap_open($this->mailbox, $username, $password);
    }
    
    
    /**
     * close connection
     */
    function __destruct() {
        if($this->imap !== false) imap_close($this->imap);
    }
   
   
    /**
     * returns true after successfull connection
     *
     * @return bool true on success
     */
    public function isConnected() {
        return $this->imap !== false;
    }
    
    
    /**
     * returns last imap error
     *
     * @return string error message
     */
    public function getError() {
        return imap_last_error();
    }
    
    
    /**
     * select given folder
     *
     * @return bool successfull opened folder
     * @param $folder name
     */
    public function selectFolder($folder) {
        $result = imap_reopen($this->imap, $this->mailbox . $folder);
        if($result === true)
            $this->folder = $folder;
        return $result;
    }
    
    
    /**
     * returns all available folders
     *
     * @return array with foldernames
     */
    public function getFolders() {
        $folders = imap_list($this->imap, $this->mailbox, "*");
        return str_replace($this->mailbox, "", $folders);
    }
    
    
    /**
     * returns the number of messages in the current folder
     *
     * @return int message count
     */
    public function countMessages() {
        return imap_num_msg($this->imap);
    }
    
    
    /**
     * returns the number of unread messages in the current folder
     *
     * @return int message count
     */
    public function countUnreadMessages() {
        $result = imap_search($this->imap, 'UNSEEN');
        if($result===false)
            return 0;
        return count($result);
    }

    /**
     * returns unseen emails in the current folder
     *
     * @return array messages
     * @param $withbody without body
     */
    public function getUnreadMessages($withbody=true){
        $emails = array();
        $result = imap_search($this->imap, 'UNSEEN');
        if($result){
            foreach($result as $k=>$i){
                $emails[]= $this->formatMessage($i, $withbody);
            }
        }
        return $emails;
    }
    
    
    /**
     * returns all emails in the current folder
     *
     * @return array messages
     * @param $withbody without body
     */
    public function getMessages($withbody = true) {
        $count = $this->countMessages();
        $emails = array();
        for($i=1;$i<=$count;$i++) {
            $emails[]= $this->formatMessage($i, $withbody);
        }

        // sort emails descending by date
        // usort($emails, function($a, $b) {
        // try {
        // $datea = new \DateTime($a['date']);
        // $dateb = new \DateTime($b['date']);
        // } catch(\Exception $e) {
        // return 0;
        // }
        // if ($datea == $dateb)
        // return 0;
        // return $datea < $dateb ? 1 : -1;
        // });

        return $emails;
    }
    
    /**
     * returns email by given id
     *
     * @return array messages
     * @param $id
     * @param $withbody without body
     */
    public function getMessage($id, $withbody = true) {
        return $this->formatMessage($id, $withbody);
    }
    
    /**
     * @param $id
     * @param bool $withbody
     * @return array
     */
    protected function formatMessage($id, $withbody=true){
        $header = imap_headerinfo($this->imap, $id);

        // fetch unique uid
        $uid = imap_uid($this->imap, $id);

        // Get the header from simply
        $fromname = $fromaddress = '';
        foreach ($header->from as $id => $object) {
            $fromname = $object->personal;
            $fromaddress = $object->mailbox . "@" . $object->host;
        }
    
        // get email data
        $subject = '';
        if ( isset($header->subject) && strlen($header->subject) > 0 ) {
            foreach(imap_mime_header_decode($header->subject) as $obj){
                $subject .= $obj->text;
            }
        }
        $subject = $this->convertToUtf8($subject);
        $email = array(
            'to'       => isset($header->to) ? $this->arrayToAddress($header->to) : '',
            'from'     => $this->convertToUtf8($fromaddress),
            'fromname' => $this->convertToUtf8($fromname),
            'date'     => $header->date,
            'subject'  => $subject,
            'uid'       => $uid,
            'unread'   => strlen(trim($header->Unseen))>0,
            'answered' => strlen(trim($header->Answered))>0,
            'deleted' => strlen(trim($header->Deleted))>0
        );
        if(isset($header->cc))
            $email['cc'] = $this->arrayToAddress($header->cc);

        // get email body
        if($withbody===true) {
            $body = $this->getBody($uid);
            $email['body'] = $body['body'];
            $email['html'] = $body['html'];
        }

        return $email;
    }
    
    /**
     * delete given message
     *
     * @return bool success or not
     * @param $id of the message
     */
    public function deleteMessage($id) {
        return $this->deleteMessages(array($id));
    }
    
    
    /**
     * delete messages
     *
     * @return bool success or not
     * @param $ids array of ids
     */
    public function deleteMessages($ids) {
        if( imap_mail_move($this->imap, implode(",", $ids), $this->getTrash(), CP_UID) == false)
            return false;
        return imap_expunge($this->imap);
    }
    
    
    /**
     * move given message in new folder
     *
     * @return bool success or not
     * @param $id of the message
     * @param $target new folder
     */
    public function moveMessage($id, $target) {
        return $this->moveMessages(array($id), $target);
    }
    
    
    /**
     * move given message in new folder
     *
     * @return bool success or not
     * @param $ids array of message ids
     * @param $target new folder
     */
    public function moveMessages($ids, $target) {
        if(imap_mail_move($this->imap, implode(",", $ids), $target, CP_UID)===false)
            return false;
        return imap_expunge($this->imap);
    }
    
    
    /**
     * mark message as read
     *
     * @return bool success or not
     * @param $id of the message
     * @param $seen true = message is read, false = message is unread
     */
    public function setUnseenMessage($id, $seen = true) {
        $header = $this->getMessageHeader($id);
        if($header==false)
            return false;
            
        $flags = "";
        $flags .= (strlen(trim($header->Answered))>0 ? "\\Answered " : '');
        $flags .= (strlen(trim($header->Flagged))>0 ? "\\Flagged " : '');
        $flags .= (strlen(trim($header->Deleted))>0 ? "\\Deleted " : '');
        $flags .= (strlen(trim($header->Draft))>0 ? "\\Draft " : '');
        
        $flags .= (($seen == true) ? '\\Seen ' : ' ');
        //echo "\n<br />".$id.": ".$flags;
        imap_clearflag_full($this->imap, $id, '\\Seen', ST_UID);
        return imap_setflag_full($this->imap, $id, trim($flags), ST_UID);
    }
    
    
    /**
     * return content of messages attachment
     *
     * @return binary attachment
     * @param $id of the message
     * @param $index of the attachment (default: first attachment)
     */
    public function getAttachment($id, $structureToExplore = null) {

        $messageIndex = imap_msgno($this->imap, $id);
        if ($structureToExplore != null) {
            $structure = $structureToExplore;
        } else {
            $structure = imap_fetchstructure($this->imap, $messageIndex);
        }
      
        $attachments = array();
       
        if (isset($structure->parts) && count($structure->parts)) {
            
            for($i = 0; $i < count($structure->parts); $i++) {
            
                if ($structure->parts && is_array($structure->parts[$i]) && count($structure->parts[$i])>0) {
                    $toAdd = $this->getAttachment($id, $structure->parts[$i]);
                    if (count($toAdd)>0) {
                        foreach ($toAdd as $att) array_push($attachments, $att);
                    }
                }
       
                $attachment = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );
               
                if ($structure->parts[$i]->ifdparameters) {
                    foreach($structure->parts[$i]->dparameters as $object) {
                        if(strtolower($object->attribute) == 'filename') {
                            $attachment['is_attachment'] = true;
                            $attachment['filename'] = $object->value;
                        }
                    }
                }
               
                if ($structure->parts[$i]->ifparameters) {
                    foreach($structure->parts[$i]->parameters as $object) {
                        if(strtolower($object->attribute) == 'name') {
                            $attachment['is_attachment'] = true;
                            $attachment['name'] = $object->value;
                        }
                    }
                }
               
                if ($attachment['is_attachment']) {
                    // get attachment body

                    // First we try the html body
                    $message = imap_fetchbody($this->imap, $messageIndex, $i+1.2);

                    if (empty($message)) {
                        // Then we try the text body
                        $message = imap_fetchbody($this->imap, $messageIndex, $i+1.1);
                    }

                    if (empty($message)) {
                        // Then we try the text body
                        $message = imap_fetchbody($this->imap, $messageIndex, $i+1);
                    }

                    switch ($structure->parts[$i]->encoding) {
                        case 0:
                        case 1:
                            $message = imap_8bit($message);
                            break;
                        case 2:
                            $message = imap_binary($message);
                            break;
                        case 3:
                            $message = base64_decode($message);
                            break;
                        case 4:
                            $message = quoted_printable_decode($message);
                            break;
                        case 5:
                            $message = imap_base64($message);
                            break;
                    }

                    $attachment['attachment'] = $message;
                    
                }
             
                array_push($attachments,$attachment);
            }
           
        }
       
        return $attachments;
    }
    
    
    /**
     * add new folder
     *
     * @return bool success or not
     * @param $name of the folder
     * @param $subscribe immediately subscribe to folder
     */
    public function addFolder($name, $subscribe = false) {
        $success = imap_createmailbox($this->imap, $this->mailbox . $name);

        if ($success && $subscribe) {
            $success = imap_subscribe($this->imap, $this->mailbox . $name);
        }

        return $success;
    }
    
    
    /**
     * remove folder
     *
     * @return bool success or not
     * @param $name of the folder
     */
    public function removeFolder($name) {
        return imap_deletemailbox($this->imap, $this->mailbox . $name);
    }
    
    
    /**
     * rename folder
     *
     * @return bool success or not
     * @param $name of the folder
     * @param $newname of the folder
     */
    public function renameFolder($name, $newname) {
        return imap_renamemailbox($this->imap, $this->mailbox . $name, $this->mailbox . $newname);
    }
    
    
    /**
     * clean folder content of selected folder
     *
     * @return bool success or not
     */
    public function purge() {
        // delete trash and spam
        if($this->folder==$this->getTrash() || strtolower($this->folder)=="spam") {
            if(imap_delete($this->imap,'1:*')===false) {
                return false;
            }
            return imap_expunge($this->imap);
        
        // move others to trash
        } else {
            if( imap_mail_move($this->imap,'1:*', $this->getTrash()) == false)
                return false;
            return imap_expunge($this->imap);
        }
    }
    
    
    /**
     * returns all email addresses
     *
     * @return array with all email addresses or false on error
     */
    public function getAllEmailAddresses() {
        $saveCurrentFolder = $this->folder;
        $emails = array();
        foreach($this->getFolders() as $folder) {
            $this->selectFolder($folder);
            foreach($this->getMessages(false) as $message) {
                $emails[] = $message['from'];
                $emails = array_merge($emails, $message['to']);
                if(isset($message['cc']))
                    $emails = array_merge($emails, $message['cc']);
            }
        }
        $this->selectFolder($saveCurrentFolder);
        return array_unique($emails);
    }
    
    
    /**
     * save email in sent
     *
     * @return void
     * @param $header
     * @param $body
     */
    public function saveMessageInSent($header, $body) {
        return imap_append($this->imap, $this->mailbox . $this->getSent(), $header . "\r\n" . $body . "\r\n", "\\Seen");
    }
    
    
    /**
     * explicitly close imap connection
     */
    public function close() {
        if($this->imap!==false)
            imap_close($this->imap);
    }
    
    
    
    // protected helpers
    
    
    /**
     * get trash folder name or create new trash folder
     *
     * @return trash folder name
     */
    protected function getTrash() {
        foreach($this->getFolders() as $folder) {
            if(strtolower($folder)==="trash" || strtolower($folder)==="papierkorb")
                return $folder;
        }
        
        // no trash folder found? create one
        $this->addFolder('Trash');
        
        return 'Trash';
    }
    
    
    /**
     * get sent folder name or create new sent folder
     *
     * @return sent folder name
     */
    protected function getSent() {
        foreach($this->getFolders() as $folder) {
            if(strtolower($folder)==="sent" || strtolower($folder)==="gesendet")
                return $folder;
        }
        
        // no sent folder found? create one
        $this->addFolder('Sent');
        
        return 'Sent';
    }
    
    
    /**
     * fetch message by id
     *
     * @return header
     * @param $id of the message
     */
    protected function getMessageHeader($id) {
        $count = $this->countMessages();
        for($i=1;$i<=$count;$i++) {
            $uid = imap_uid($this->imap, $i);
            if($uid==$id) {
                $header = imap_headerinfo($this->imap, $i);
                return $header;
            }
        }
        return false;
    }
    
    
    /**
     * convert imap given address in string
     *
     * @return string in format "Name <email@bla.de>"
     * @param $headerinfos the infos given by imap
     */
    protected function toAddress($headerinfos) {
        $email = "";
        $name = "";
        if(isset($headerinfos->mailbox) && isset($headerinfos->host)) {
            $email = $headerinfos->mailbox . "@" . $headerinfos->host;
        }

        if(!empty($headerinfos->personal)) {
            $name = imap_mime_header_decode($headerinfos->personal);
            $name = $name[0]->text;
        } else {
            $name = $email;
        }
        
        $name = $this->convertToUtf8($name);
        
        return $name . " <" . $email . ">";
    }

    
    /**
     * converts imap given array of addresses in strings
     *
     * @return array with strings (e.g. ["Name <email@bla.de>", "Name2 <email2@bla.de>"]
     * @param $addresses imap given addresses as array
     */
    protected function arrayToAddress($addresses) {
        $addressesAsString = array();
        foreach($addresses as $address) {
            $addressesAsString[] = $this->toAddress($address);
        }
        return $addressesAsString;
    }

    
    /**
     * returns body of the email. First search for html version of the email, then the plain part.
     *
     * @return string email body
     * @param $uid message id
     */
    protected function getBody($uid) {
        $body = $this->get_part($this->imap, $uid, "TEXT/PLAIN");
        $html = false;
        // if text body is empty, try getting text body
        if ($body == "") {
            $body = $this->get_part($this->imap, $uid, "TEXT/HTML");
            $html = true;
        }
        $body = $this->convertToUtf8($body);
        return array( 'body' => $body, 'html' => $html);
    }
    
    
    /**
     * convert to utf8 if necessary.
     *
     * @return true or false
     * @param $string utf8 encoded string
     */
    protected function convertToUtf8($string, $charset = 'utf-8') {
        $newString = '';
        $elements = imap_mime_header_decode($string);

        for($i = 0; $i < count($elements); $i++) {
            if($elements[$i]->charset == 'default') {
                $elements[$i]->charset = 'iso-8859-1';
            }
            $newString .= $this->convertStringEncoding($elements[$i]->text, $elements[$i]->charset, $charset);
        }
        return $newString;
    }

    /**
     * Converts a string from one encoding to another.
     * @param string $string
     * @param string $fromEncoding
     * @param string $toEncoding
     * @return string Converted string if conversion was successful, or the original string if not
     */
    protected function convertStringEncoding($string, $fromEncoding, $toEncoding) {
        $convertedString = null;
        if($string && $fromEncoding != $toEncoding) {
            $convertedString = @iconv($fromEncoding, $toEncoding . '//IGNORE', $string);
            if(!$convertedString && extension_loaded('mbstring')) {
                $convertedString = @mb_convert_encoding($string, $toEncoding, $fromEncoding);
            }
        }
        return $convertedString ?: $string;
    }
    
    
    /**
     * returns a part with a given mimetype
     * taken from http://www.sitepoint.com/exploring-phps-imap-library-2/
     *
     * @return string email body
     * @param $imap imap stream
     * @param $uid message id
     * @param $mimetype
     */
    protected function get_part($imap, $uid, $mimetype, $structure = false, $partNumber = false) {
        if (!$structure) {
               $structure = imap_fetchstructure($imap, $uid, FT_UID);
        }
        if ($structure) {
            if ($mimetype == $this->get_mime_type($structure)) {
                if (!$partNumber) {
                    $partNumber = 1;
                }
                $text = imap_fetchbody($imap, $uid, $partNumber, FT_UID | FT_PEEK);
                switch ($structure->encoding) {
                    case 3: return imap_base64($text);
                    case 4: return imap_qprint($text);
                    default: return $text;
               }
           }
     
            // multipart 
            if ($structure->type == 1) {
                foreach ($structure->parts as $index => $subStruct) {
                    $prefix = "";
                    if ($partNumber) {
                        $prefix = $partNumber . ".";
                    }
                    $data = $this->get_part($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));
                    if ($data) {
                        return $data;
                    }
                }
            }
        }
        return false;
    }
    
    
    /**
     * extract mimetype
     * taken from http://www.sitepoint.com/exploring-phps-imap-library-2/
     *
     * @return string mimetype
     * @param $structure
     */
    protected function get_mime_type($structure) {
        $primaryMimetype = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
     
        if ($structure->subtype) {
           return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
        }
        return "TEXT/PLAIN";
    }
    
    
    /**
     * Return general mailbox statistics
     *
     * @return bool | StdClass object
     */
    public function getMailboxStatistics() {
        return $this->isConnected() ? imap_mailboxmsginfo($this->imap) : false ;
    }

}
?>