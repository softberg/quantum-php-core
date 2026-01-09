<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 3.0.0
 */

namespace Quantum\Libraries\Mailer\Contracts;

/**
 * Interface MailerInterface
 * @package Quantum\Libraries\Mailer
 */
interface MailerInterface
{
    /**
     * Sets the 'From' email and the name
     * @param string $email
     * @param string|null $name
     * @return MailerInterface
     */
    public function setFrom(string $email, ?string $name = null): MailerInterface;

    /**
     * Gets the 'From' email and the name
     * @return array
     */
    public function getFrom(): array;

    /**
     * Sets 'To' addresses
     * @param string $email
     * @param string|null $name
     * @return MailerInterface
     */
    public function setAddress(string $email, ?string $name = null): MailerInterface;

    /**
     * Gets 'To' addresses
     * @return array
     */
    public function getAddresses(): array;

    /**
     * Sets the subject
     * @param string|null $subject
     * @return MailerInterface
     */
    public function setSubject(?string $subject): MailerInterface;

    /**
     * Gets the subject
     * @return string|null
     */
    public function getSubject(): ?string;

    /**
     * Sets the template
     * @param string $templatePath
     * @return MailerInterface
     */
    public function setTemplate(string $templatePath): MailerInterface;

    /**
     * Gets the template
     * @return string|null
     */
    public function getTemplate(): ?string;

    /**
     * Sets the body
     * @param string|array $message
     * @return MailerInterface
     */
    public function setBody($message): MailerInterface;

    /**
     * Gets the body
     * @return string|array
     */
    public function getBody();

    /**
     * Sends an email
     * @return bool
     */
    public function send(): bool;
}
