<?php
/**
 * @see https://laracasts.com/lessons/flexible-flash-messages
 *
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Rutorika\Dashboard\Notifications;


use Illuminate\Session\Store;

/**
 * Class FlashNotifier
 *
 * @package Rutorika\Dashboard\Notifications
 */
class FlashNotifier
{

    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const DANGER = 'danger';
    const ERROR = 'danger';

    /**
     * @var Store
     */
    private $session;

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    public function success($message)
    {
        $this->message($message, static::SUCCESS);
    }

    public function info($message)
    {
        $this->message($message, static::INFO);
    }

    public function warning($message)
    {
        $this->message($message, static::WARNING);
    }

    public function error($message)
    {
        $this->message($message, static::ERROR);
    }

    public function danger($message)
    {
        $this->message($message, static::DANGER);
    }

    public function message($message, $level = 'info')
    {
        $this->session->flash(
            'flash_notification',
            [
                'message' => $message,
                'level' => $level,
            ]);
    }

    public function formInvalid()
    {
        $this->error('Проверьте правильность введённых данных');
    }
}
