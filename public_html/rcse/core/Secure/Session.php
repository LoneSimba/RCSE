<?php


namespace RCSE\Core\Secure;


class Session
{
    public const SESSION_STARTED = TRUE;
    public const SESSION_NOT_STARTED = FALSE;

    // The state of the session
    private bool $sessionState = self::SESSION_NOT_STARTED;


    public function __construct(string $id = "")
    {
        if(!empty($id)) $this->setId($id);
        $this->start();
    }


    /**
     *    (Re)starts the session.
     *
     *    @return bool
     **/

    public function start() : bool
    {
        if ( $this->sessionState == self::SESSION_NOT_STARTED )
        {
            $this->sessionState = session_start();
        }

        return $this->sessionState;
    }

    /**
     *    Destroys the current session.
     *
     *    @return bool
     **/
    public function destroy() : bool
    {
        if ($this->sessionState == self::SESSION_STARTED)
        {
            $this->sessionState = !session_destroy();
            unset( $_SESSION );

            return !$this->sessionState;
        }

        return false;
    }

    public function setId(string $id) : bool
    {
        return session_id($id);
    }

    /**
     *    Stores value in the session.
     *    Example: $instance->foo = 'bar';
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */

    public function __set(string $name, $value) : void
    {
        $_SESSION[$name] = $value;
    }

    /**
     *    Gets value from the session.
     *    Example: echo $instance->foo;
     *
     * @param string $name
     * @return mixed
     */

    public function __get(string $name)
    {
        if ( isset($_SESSION[$name]))
        {
            return $_SESSION[$name];
        }

        return null;
    }

    public function __isset(string $name) : bool
    {
        return isset($_SESSION[$name]);
    }

    public function __unset(string $name)
    {
        unset($_SESSION[$name]);
    }

}