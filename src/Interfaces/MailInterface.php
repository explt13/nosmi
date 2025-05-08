<?php

namespace Explt13\Nosmi\Interfaces;

interface MailInterface
{
    
    /**
     * Sets the subject of the email.
     *
     * @param string $subject The subject of the email.
     * @return static Returns the current instance for method chaining.
     */
    public function withSubject(string $subject): static;

    /**
     * Sets the HTML content of the email.
     *
     * @param string $html The HTML content of the email.
     * @param string|null $is_path Optional. If provided, indicates the HTML content is a file path.
     * @return static Returns the current instance for method chaining.
     */
    public function withHtml(string $html, ?string $is_path = null): static;

    /**
     * Sets the plain text content of the email.
     *
     * @param string $plain The plain text content of the email.
     * @param string|null $is_path Optional. If provided, indicates the plain text content is a file path.
     * @return static Returns the current instance for method chaining.
     */
    public function withPlain(string $plain, ?string $is_path = null): static;

    /**
     * Sets the alternative content of the email.
     *
     * @param string $alt The alternative content of the email.
     * @param string|null $is_path Optional. If provided, indicates the alternative content is a file path.
     * @return static Returns the current instance for method chaining.
     */
    public function withAlt(string $alt, ?string $is_path = null): static;

    /**
     * Enables verbose debugging for the email sending process.
     *
     * @return static Returns the current instance for method chaining.
     */
    public function withVerboseDebug(): static;

    /**
     * Adds a recipient to the email.
     *
     * @param string $recipient_email The email address of the recipient.
     * @return static Returns the current instance for method chaining.
     */
    public function withRecipient(string $recipient_email): static;

    /**
     * Adds multiple recipients to the email.
     *
     * @param array $recipient_emails An array of recipient email addresses.
     * @return static Returns the current instance for method chaining.
     */
    public function withRecipients(array $recipient_emails): static;

    /**
     * Allows the mail content to be empty.
     *
     * @return static Returns an instance of the implementing class.
     */
    public function allowEmptyContent(): static;

    /**
     * Sends the email.
     *
     * @return void
     */
    public function send(): void;
}