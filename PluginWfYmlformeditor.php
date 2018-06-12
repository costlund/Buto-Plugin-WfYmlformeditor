<?php
/**
Edit forms.
Only if user has role ymlformeditor.
In theme /config/settings.yml
plugin_modules:
  ymlformeditor:
    plugin: 'wf/ymlformeditor'
    settings:
      dir: '/theme/[theme]/data/ymlformeditor'
 */
class PluginWfYmlformeditor{
  function __construct($buto) {
    if($buto){
      wfPlugin::includeonce('wf/form_v2');
      wfPlugin::enable('wf/bootstrap');
      wfPlugin::enable('wf/form_v2');
      wfPlugin::includeonce('wf/array');
    }
  }
  /**
   * Start page.
   */
  public static function page_home(){
    wfArray::set($GLOBALS, 'sys/layout_path', '/plugin/wf/ymlformeditor/layout');
    $page = wfFilesystem::loadYml(__DIR__.'/page/home.yml');
    wfDocument::mergeLayout($page);
  }
  /**
   * List all editable files in dir.
  */
  public static function page_forms(){
    $settings = wfPlugin::getModuleSettings();
    wfArray::set($GLOBALS, 'sys/layout_path', '/plugin/wf/ymlformeditor/layout');
    if(!wfArray::get($settings, 'dir')){
      exit('Dir is not set...');
    }
    $dir = wfArray::get($GLOBALS, 'sys/app_dir').wfArray::get($settings, 'dir');
    $yml_files = wfFilesystem::getScandir($dir);
    $item = array();
    $class = wfArray::get($GLOBALS, 'sys/class');
    foreach ($yml_files as $key => $value) {
      $yml = urlencode($value);
      $yml = substr($yml, 0, strlen($yml)-4);
      if(is_dir($dir.'/'.$value)){
        $item[] = array('href' => '#', 'innerHTML' => '<i class="fa fa-folder"></i> '.$value, 'onclick' => 'return false;', 'onclickzzz' => "PluginWfBootstrapjs.modal({id: 'wf_edit_list', url: '/$class/list/yml/$yml', lable: 'List'});");
      }elseif(strtoupper (substr($value, strlen($value)-4, 4))=='.YML'){
        $yml_form = new PluginWfArray(wfFilesystem::loadYml($dir.'/'.$value));
        if($yml_form->get('multiple_items')){
          $item[] = array('href' => '#', 'innerHTML' => '<i class="fa fa-file-text-o"></i> '.$yml_form->get('name'), 'onclick' => "PluginWfBootstrapjs.modal({id: 'wf_edit_list', url: '/$class/list/yml/$yml', lable: 'List'});return false;");
        }else{
          if(!$yml_form->get('preview_skip')){
            $item[] = array('href' => '#', 'innerHTML' => '<i class="fa fa-file-text-o"></i> '.$yml_form->get('name'), 'onclick' => "PluginWfBootstrapjs.modal({id: 'wf_edit_view', url: '/$class/view/yml/$yml', lable: '".$yml_form->get('name')."', fade: false});return false;");
          }else{
            $item[] = array('href' => '#', 'innerHTML' => '<i class="fa fa-file-text-o"></i> '.$yml_form->get('name'), 'onclick' => "PluginWfBootstrapjs.modal({id: 'ymlformeditor_edit', url: '/$class/edit/yml/$yml', lable: '".$yml_form->get('name')."', fade: false});return false;");
          }
        }
      }
    }
    $filename = wfArray::get($GLOBALS, 'sys/app_dir').'/plugin/wf/ymlformeditor/page/forms.yml';
    $page = wfFilesystem::loadYml($filename);
    $listgroup = wfDocument::createWidget('wf/bootstrap', 'listgroup', array('item' => $item));
    $page = wfArray::set($page, 'content/list', $listgroup);
    wfDocument::mergeLayout($page);
  }
  public static function page_list(){
    $data = self::init_data();
    $yml_form = $data['yml_form'];
    $file = $data['file'];
    $class = wfArray::get($GLOBALS, 'sys/class');
    if(wfArray::get($yml_form, 'key')){
      $table_data_data = wfArray::get($file, wfArray::get($yml_form, 'key'));
    }else{
      $table_data_data = $file;
    }
    if(wfArray::get($yml_form, 'list/order_by')){
      foreach (wfArray::get($yml_form, 'list/order_by') as $key => $value) {
        $table_data_data = wfArray::sortMultiple($table_data_data, wfArray::get($value, 'name'), wfArray::get($value, 'desc'));
      }
    }
    foreach ($table_data_data as $key => $value) {
      $onclick = "PluginWfBootstrapjs.modal({id: 'wf_edit_view', url: '/$class/view/yml/".wfRequest::get('yml')."/key/$key', lable: 'Form', fade: false});";
      $table_data_data[$key]['attribute'] = array('onclick' => $onclick, 'style' => 'cursor:pointer;');
    }
    $table_data = array();
    $table_data['fields'] = wfArray::get($yml_form, 'list/item');
    $table_data['data'] = $table_data_data;
    $table = wfDocument::createWidget('wf/table', 'render', $table_data);
    $element = array();
    $element[] = $table;
    // New post button.
    $element[] = wfDocument::createHtmlElement('script', "if(!document.getElementById('btn_new')){ PluginWfDom.render([{type: 'button', attribute: {type: 'button', class: 'btn btn-default', id: 'btn_new'}, innerHTML: 'New'}], document.getElementById('wf_edit_list_footer')); } ");
    $onclick = "PluginWfBootstrapjs.modal({id: 'ymlformeditor_edit', url: '/ymlformeditor/edit/yml/".wfRequest::get('yml')."', lable: 'Edit'});";
    $element[] = wfDocument::createHtmlElement('script', "document.getElementById('btn_new').onclick = function(){ $onclick } ");
    wfArray::set($GLOBALS, 'sys/layout_path', '/plugin/wf/ymlformeditor/layout');
    $page = wfFilesystem::loadYml(__DIR__.'/page/list.yml');
    $page = wfArray::set($page, 'content', $element);
    wfDocument::mergeLayout($page);
  }
  private static function init_data(){
    $settings = wfPlugin::getModuleSettings();
    $yml_form = wfFilesystem::loadYml(wfArray::get($GLOBALS, 'sys/app_dir').'/'.wfArray::get($settings, 'dir').'/'.wfRequest::get('yml').'.yml');
    $file = wfFilesystem::loadYml(wfArray::get($GLOBALS, 'sys/app_dir').'/'.wfArray::get($yml_form, 'file')) ; // File to edit.
    $form =  self::setForm($yml_form, wfRequest::get('yml'), wfRequest::get('key')); // Edit form.
    $class = wfArray::get($GLOBALS, 'sys/class');
    return array('settings' => $settings, 'yml_form' => $yml_form, 'file' => $file, 'form' => $form, 'class' => $class);
  }
  private static function init_upload_form($data){
    $form_upload = wfFilesystem::loadYml(__DIR__.'/config/upload_form.yml');
    $form_upload = wfArray::set($form_upload, 'url', '/'.wfArray::get($GLOBALS, 'sys/class').'/uploadsend?yml='.wfRequest::get('yml').'&files='.wfRequest::get('files').'&key='.wfRequest::get('key'));
    $name = wfArray::get($data, 'yml_form/files/'.wfRequest::get('files').'/name');
    $name = str_replace('[key]', wfRequest::get('key'), $name);
    $form_upload = wfArray::set($form_upload, 'name', $name);
    $form_upload = wfArray::set($form_upload, 'dir', wfArray::get($data, 'yml_form/files/'.wfRequest::get('files').'/dir'));
    $form_upload = wfArray::set($form_upload, 'accept', wfArray::get($data, 'yml_form/files/'.wfRequest::get('files').'/type'));
    $form_upload = wfArray::set($form_upload, 'file_types', array(wfArray::get($data, 'yml_form/files/'.wfRequest::get('files').'/file_type')));
    return $form_upload;
  }
  public static function page_view(){
    $data = self::init_data();
    $form = $data['form'];
    $element = array();
    $element[] = wfDocument::createHtmlElement('div', wfArray::get($form, 'description'));
    if(wfArray::isKey($form, 'form/data/items')){
      foreach (wfArray::get($form, 'form/data/items') as $key => $value) {
        if(wfArray::get($value, 'type') == 'hidden'){ continue; }
        $element[] = wfDocument::createHtmlElement('div', wfArray::get($value, 'label'), array('style' => 'font-weight:bold'));
        $element[] = wfDocument::createHtmlElement('div', str_replace("\n", '<br>', self::handleOutput(wfArray::get($value, 'default'))), array('style' => 'min-height:20px;'));
      }
      $onclick = "PluginWfBootstrapjs.modal({id: 'ymlformeditor_edit', url: '/".wfArray::get($GLOBALS, 'sys/class')."/edit/yml/".wfRequest::get('yml')."?key=".wfRequest::get('key')."', lable: 'Edit'});";
      // Edit button.
      $element[] = wfDocument::createHtmlElement('script', "if(!document.getElementById('btn_edit')){ PluginWfDom.render([{type: 'button', attribute: {type: 'button', class: 'btn btn-default', id: 'btn_edit'}, innerHTML: 'Edit'}], document.getElementById('wf_edit_view_footer')); } ");
      $element[] = wfDocument::createHtmlElement('script', "document.getElementById('btn_edit').onclick = function(){ $onclick } ");
    }
    if(wfArray::isKey($form, 'files')){
      $element[] = wfDocument::createHtmlElement('div', "load:/[class]/file/yml/".wfRequest::get('yml')."/key/".wfRequest::get('key'), array('id' => "yml_editor_file_".wfRequest::get('key')));
    }
    wfArray::set($GLOBALS, 'sys/layout_path', '/plugin/wf/ymlformeditor/layout');
    $page = wfFilesystem::loadYml(__DIR__.'/page/edit.yml');
    $page = wfArray::set($page, 'content', $element);
    wfDocument::mergeLayout($page);
  }
  private static function handleOutput($value){
    if($value === true){
      return 'True';
    }elseif($value === false){
      return 'False';
    }elseif($value === 0 || $value === '0'){
      return '0&nbsp;';
    }else{
      return $value;
    }
  }
  public static function page_file(){
    $data = self::init_data();
    $class = $data['class'];
    $element = array();
    foreach (wfArray::get($data, 'form/files') as $key => $value) {
      $name = wfArray::get($value, 'name');
      $name = str_replace('[key]', wfRequest::get('key'), $name);
      $dir = wfSettings::replaceDir(wfArray::get($value, 'dir'));
      $dir .= '/'.$name;
      $src = wfSettings::replaceTheme(wfArray::get($value, 'dir'));
      $src = str_replace('[web_dir]', '', $src);
      $src .= '/'.$name;
      $src .= '?filetime='.wfFilesystem::getFiletime($dir);
      $rewrite = array();
      $rewrite['col/innerHTML/thumbnail/innerHTML/div/innerHTML/h3/innerHTML'] = wfArray::get($value, 'lable');
      $rewrite['col/innerHTML/thumbnail/innerHTML/div/innerHTML/p1/innerHTML'] = array(
        wfDocument::createHtmlElement('div', array(
          wfDocument::createHtmlElement('strong', 'Dir: '),
          wfDocument::createHtmlElement('span', wfArray::get($value, 'dir'))
        )),
        wfDocument::createHtmlElement('div', array(
          wfDocument::createHtmlElement('strong', 'Filename: '),
          wfDocument::createHtmlElement('span', wfArray::get($value, 'name'))
        ))
        
      );
      if(wfFilesystem::fileExist($dir)){
        $onclick = "if(confirm('Are you sure?')){ $.get('/$class/deletefile/yml/".wfRequest::get('yml')."/files/".$key."/key/".wfRequest::get('key')."',   function(data) { PluginWfCallbackjson.call( data ); }  );}";
        $rewrite['col/innerHTML/thumbnail/innerHTML/div/innerHTML/p2/innerHTML/a/innerHTML'] = 'Delete';
        $rewrite['col/innerHTML/thumbnail/innerHTML/div/innerHTML/p2/innerHTML/a/attribute/onclick'] = $onclick;
        $rewrite['col/innerHTML/thumbnail/innerHTML/img/attribute/src'] = $src;
        $rewrite['col/innerHTML/thumbnail/innerHTML/img/attribute/title'] = $src;
        $rewrite['col/innerHTML/thumbnail/innerHTML/div/innerHTML/p2/innerHTML/a/attribute/class'] = 'btn btn-warning';
      }else{
        $onclick = "PluginWfBootstrapjs.modal({id: 'wf_edit_upload', url: '/".$data['class']."/upload/yml/".wfRequest::get('yml')."/files/$key/key/".wfRequest::get('key')."', lable: 'Upload $name'});";
        $rewrite['col/innerHTML/thumbnail/innerHTML/div/innerHTML/p2/innerHTML/a/innerHTML'] = 'Upload';
        $rewrite['col/innerHTML/thumbnail/innerHTML/div/innerHTML/p2/innerHTML/a/attribute/onclick'] = $onclick;
        $rewrite['col/innerHTML/thumbnail/innerHTML/img/attribute/style'] = 'display:none';
      }
      $thumbnail = array('rewrite' => $rewrite);
      $element[] = wfDocument::createWidget('wf/bootstrap', 'thumbnail', $thumbnail);
    }
    $element = wfDocument::createHtmlElement('div', $element, array('class' => 'row'));
    wfArray::set($GLOBALS, 'sys/layout_path', '/plugin/wf/ymlformeditor/layout');
    $page = wfFilesystem::loadYml(__DIR__.'/page/file.yml');
    $page = wfArray::set($page, 'content/main/innerHTML', array($element));
    wfDocument::mergeLayout($page);
  }
  public static function page_deletefile(){
    $data = self::init_data();
    if(self::delete_file($data, wfRequest::get('files'), wfRequest::get('key'))){
      $json = array('success' => true, 'update' => array('wf_edit_view_body'));
    }  else {
      $json = array('success' => false, 'alert' => array("Could not find file to delete!"));
    }
    exit(json_encode($json));
  }
  private static function delete_file($data, $file_key, $key){
    if(!wfArray::isKey($data, 'yml_form/files/'.$file_key.'/name')){
      return false;
    }
    if(!wfArray::isKey($data, 'yml_form/files/'.$file_key.'/dir')){
      return false;
    }
    $name = wfArray::get($data, 'yml_form/files/'.$file_key.'/name');
    $name = str_replace('[key]', $key, $name);
    $dir = wfArray::get($data, 'yml_form/files/'.$file_key.'/dir');
    $dir = wfSettings::replaceDir($dir);
    $dir .= '/'.$name;
    if(wfFilesystem::fileExist($dir)){
      wfFilesystem::delete($dir);
      return true;
    }else{
      return false;
    }
  }
  public static function page_upload(){
    $data = self::init_data();
    $form_upload = self::init_upload_form($data);
    $element = array();
    $element[] = wfDocument::createWidget('wf/formupload', 'render', $form_upload);
    wfArray::set($GLOBALS, 'sys/layout_path', '/plugin/wf/ymlformeditor/layout');
    $page = wfFilesystem::loadYml(__DIR__.'/page/upload.yml');
    $page = wfArray::set($page, 'content', $element);
    wfDocument::mergeLayout($page);
  }
  public static function page_uploadsend(){
    $data = self::init_data();
    $form_upload = self::init_upload_form($data);
    $element = array();
    $element[] = wfDocument::createWidget('wf/formupload', 'upload', $form_upload);
    wfArray::set($GLOBALS, 'sys/layout_path', '/plugin/wf/ymlformeditor/layout');
    $page = wfFilesystem::loadYml(__DIR__.'/page/upload.yml');
    $page = wfArray::set($page, 'content', $element);
    wfDocument::mergeLayout($page);
  }
  public static function page_edit(){
    $data = self::init_data();
    $form = $data['form'];
    $element = array();
    $element[] = wfDocument::createWidget('wf/form_v2', 'render', wfArray::get($form, 'form/data'));
    /**
     * Delete button (as a option).
     */
    if(wfRequest::get('key')){
      $class = wfArray::get($GLOBALS, 'sys/class');
      $onclick = "if(confirm('Are you sure?')){ $.get('/$class/delete/yml/".wfRequest::get('yml')."/key/".wfRequest::get('key')."',   function(data) { PluginWfCallbackjson.call( data ); }  );}";
    }
    wfArray::set($GLOBALS, 'sys/layout_path', '/plugin/wf/ymlformeditor/layout');
    $page = wfFilesystem::loadYml(__DIR__.'/page/edit.yml');
    $page = wfArray::set($page, 'content', $element);
    wfDocument::mergeLayout($page);
  }
  /**
   * Only delete if multiple items.
   */
  public static function page_delete(){
    $data = self::init_data();
    $yml_form = $data['yml_form'];
    $file = $data['file'];
    $files = wfArray::get($data, 'yml_form/files');
    /**
     * Delete files.
     */
    if($files){
      foreach ($files as $key => $value) {
        self::delete_file($data, $key, wfRequest::get('key'));
      }
    }
    /**
     * Delete data in yml file.
     */
    if(wfArray::get($yml_form, 'key')){
      $file = wfArray::setUnset($file, $yml_form['key'].'/'.wfRequest::get('key'));
    }  else {
      $file = wfArray::setUnset($file, wfRequest::get('key'));
    }
    /**
     * Update yml file.
     */
    $dir = wfArray::get($GLOBALS, 'sys/app_dir').wfSettings::replaceTheme($yml_form['file']);
    wfSettings::setSettings($dir, $file);
    /**
     * Output json.
     */
    $json = array('success' => true, 'update' => array('wf_edit_list_body'), 'script' => array("$('#ymlformeditor_edit').modal('hide');$('#wf_edit_view').modal('hide');"), 'clickzzz' => array('ymlformeditor_edit_btn_close', 'wf_edit_view_btn_close'));
    exit(json_encode($json));
  }  
  
  public static function page_update(){
    $settings = wfPlugin::getModuleSettings(); // Module settings including folder dir.
    $yml_form = wfFilesystem::loadYml(wfArray::get($GLOBALS, 'sys/app_dir').'/'.wfArray::get($settings, 'dir').'/'.wfRequest::get('yml').'.yml'); // The form.
    $file = wfFilesystem::loadYml(wfArray::get($GLOBALS, 'sys/app_dir').'/'.wfArray::get($yml_form, 'file')) ; // File to edit.
    $form =  self::setForm($yml_form, wfRequest::get('yml'), wfRequest::get('key')); // Edit form.
    $yml_key = wfRequest::get('key');
    if(!$yml_key){
      $yml_key = wfCrypt::getUid();
    }
    $form_v2 = new PluginWfForm_v2(true);
    $form['form']['data'] = $form_v2->bindAndValidate($form['form']['data']);
    if($form['form']['data']['is_valid']){
      foreach (wfArray::get($form, 'form/data/items') as $key => $value) {
        if($key=='yml' || $key=='key'){ continue;}
        if(wfArray::get($yml_form, 'multiple_items')){
          if(wfArray::get($yml_form, 'key')){
            $file = wfArray::set($file, $yml_form['key'].'/'.$yml_key.'/'.$key, wfArray::get($value, 'post_value'));
          }  else {
            $file = wfArray::set($file, $yml_key.'/'.$key, wfArray::get($value, 'post_value'));
          }
        }  else {
          if(wfArray::get($yml_form, 'key')){
            $file = wfArray::set($file, $yml_form['key'].'/'.$key, wfArray::get($value, 'post_value'));
          }  else {
            $file = wfArray::set($file, $key, wfArray::get($value, 'post_value'));
          }
        }
      }
      $dir = wfArray::get($GLOBALS, 'sys/app_dir').wfSettings::replaceTheme($yml_form['file']);
      wfSettings::setSettings($dir, $file);
      $json = array(
          'success' => true,
          'updatezzz' => array('wf_edit_list_body'),
          'update' => array('wf_edit_view_body', 'wf_edit_list_body'), 
          'script' => array("$('#ymlformeditor_edit').modal('hide');"));
    }else{
      $json = array('success' => false, 'alert' => array('errors...'));
    }
    exit(json_encode($json));
  }
  private static function setForm($yml, $yml_file, $yml_key){
    if(wfArray::get($yml, 'multiple_items')){
      /**
       * Multiple items.
       */
      if($yml_key){
        if(wfArray::get($yml, 'key')){
          $data = wfSettings::getSettings(wfSettings::replaceTheme($yml['file']), $yml['key'].'/'.$yml_key);
        }else{
          $data = wfSettings::getSettings(wfSettings::replaceTheme($yml['file']), $yml_key);
        }
        foreach (wfArray::get($yml, 'form/data/items') as $key => $value) {
          if($value['type']=='div') continue;
          $yml = wfArray::set($yml, "form/data/items/$key/default", self::htmlentities(wfArray::get($data, $key)));
        }
        $yml = wfArray::set($yml, "form/data/buttons/btn_delete", wfDocument::createHtmlElement('a', 'Delete', array('onclick' => "if(confirm('Are you sure?')){ $.get( '/ymlformeditor/delete/yml/$yml_file/key/$yml_key', function(data){ PluginWfCallbackjson.call(data); }); }")));
      }
    }else{
      /**
       * One item.
       */
      if(wfArray::get($yml, 'file') && wfArray::get($yml, 'key')){
        $data = wfSettings::getSettings(wfSettings::replaceTheme($yml['file']), $yml['key']);
      }elseif(wfArray::get($yml, 'file')){
        $data = wfSettings::getSettings(wfSettings::replaceTheme($yml['file']));
      }
      if(wfArray::isKey($yml, 'form/data/items')){
        foreach (wfArray::get($yml, 'form/data/items') as $key => $value) {
          if($value['type']=='div') continue;
          $yml = wfArray::set($yml, "form/data/items/$key/default", self::htmlentities(wfArray::get($data, $key)));
        }
      }
    }
    if(wfArray::isKey($yml, 'form/data/items')){
      $yml = wfArray::set($yml, 'form/data/items/yml', array('default' => $yml_file, 'type' => 'hidden', 'label' => 'yml'));
      $yml = wfArray::set($yml, 'form/data/items/key', array('default' => $yml_key, 'type' => 'hidden', 'label' => 'key'));
    }
    $yml = wfArray::set($yml, 'form/data/submit_value', __('Save'));
    $yml = wfArray::set($yml, 'form/data/url', '/[class]/update');
    $yml = wfArray::set($yml, 'form/data/ajax', true);
    return $yml;
  }
  /**
   * Handle issue where htmlentities convert boolean to 0 or 1.
   * @param type $value
   * @return type
   */
  private static function htmlentities($value){
    if($value === true || $value === false){
      return $value;
    }else{
      return $value;
    }
  }
}
