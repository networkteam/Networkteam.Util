<?php
namespace Networkteam\Util\Mail;

/***************************************************************
 *  (c) 2019 networkteam GmbH - all rights reserved
 ***************************************************************/
class MailHeader
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * MailHeader constructor.
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}