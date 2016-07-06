<?php
class email_tpl_model extends Model
{
    public $table_name = 'email_template';
    
    public $rules = array
    (
        'name' => array
        (
            'is_required' => array(TRUE, '模板名称不能为空'),
            'max_length' => array(50, '模板名称不能超过50个字符'), 
        ),
        'id' => array
        (
            'is_required' => array(TRUE, '模板索引不能为空'),
        ),
        'subject' => array
        (
            'is_required' => array(TRUE, '邮件主题不能为空'),
            'max_length' => array(240, '邮件主题不能超过240个字符'), 
        ),
        'body' => array
        (
            'is_required' => array(TRUE, '模板名称不能为空'),
        ),
    );
    
    public $addrules = array
    (
        'id' => array
        (
            'addrule_id_exist' => '模板索引已存在',
            'addrule_id_format' => '模板索引格式不正确',
        ),
    );
    
    //自定义验证器：检查模板索引是否存在
    public function addrule_id_exist($val)
    {
        if($this->find(array('id' => $val))) return FALSE;
        return TRUE;
    }
    
    //自定义验证器: 检查模板索引格式是否正确
    public function addrule_id_format($val)
    {
        return preg_match('/^$|^[0-9_a-zA-Z]{1,30}$/', $val) != 0;
    }
    
    public function save_tpl_file($id, $content)
    {
        $path = APP_DIR.DS.'view'.DS.'mail'.DS.$id.'.html';
        file_put_contents($path, $content);
    }
    
    public function fetch_tpl($name, $vars)
    {
        $view = new View(VIEW_DIR.DS.'mail', APP_DIR.DS.'protected'.DS.'cache'.DS.'template');
        $view->assign($vars);
        return $view->render($name.'.html');
    }
    
    public function get_tpl_file($id)
    {
        return @file_get_contents(VIEW_DIR.DS.'mail'.DS.$id.'.html');
    }
    
    public function sendmail($id, $email, $tpl_vars)
    {
        if($tpl = $this->find(array('id' => $id), null, 'subject, is_html'))
        {
            $data = array
            (
                'tpl_id' => $id,
                'email' => $email,
                'subject' => $tpl['subject'],
                'body' => $this->fetch_tpl($id, $tpl_vars),
                'is_html' => $tpl['is_html'],
                'dateline' => $_SERVER['REQUEST_TIME'],
            );
            $queue = new email_queue_model();
            $queue_id = $queue->create($data);
            return $queue->send($queue_id);
        }
        return FALSE;
    }
    
    public function count_send_times($type, $user_id = 0, $ip = '')
    {
        $limit_model = new sendmail_limit_model();
        $row = array('type' => $type, 'user_id' => $user_id, 'ip' => $ip, 'dateline' => strtotime(date('Ymd')));
        if($limit_model->incr($row, 'count') == 0) $limit_model->create($row);
    }
    
    public function check_send_count($type, $user_id = 0, $ip = '')
    {
        $limit_model = new sendmail_limit_model();
        if($count = $limit_model->find(array('type' => $type, 'user_id' => $user_id, 'ip' => $ip, 'dateline' => strtotime(date('Ymd'))), null, 'count'))
        {
            if($count['count'] >= 5) return FALSE;
        }
        return TRUE;
    }
}
