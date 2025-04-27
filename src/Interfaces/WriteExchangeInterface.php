<?php

namespace Explt13\Nosmi\Interfaces;

interface WriteExchangeInterface
{
    /**
     * Sets the request body as form-encoded data.
     *
     * @param array $data An associative array of form data.
     * @return static Returns a new instance with the updated form body.
     */
    public function withFormBody(array $data): static;
     

    /**
     * Sets the request body as JSON-encoded data.
     *
     * @param array $data An associative array of data to be JSON-encoded.
     * @return static Returns a new instance with the updated JSON body.
     */
    public function withJson(array $data): static;
     
    
    /**
     * Sets the request body as raw XML.
     *
     * @param string $xml A string containing the XML data.
     * @return static Returns a new instance with the updated XML body.
     */
    public function withXml(string $xml): static;
     
    
    /**
     * Sets the request body as multipart form data.
     *
     * @param array $fields An associative array of form fields.
     * @param array $files An associative array of files to be included in the multipart request.
     * @return static Returns a new instance with the updated multipart body.
     */
    public function withMultipartBody(array $fields, array $files): static;
     
    
    /**
     * Adds a Bearer token to the request's Authorization header.
     *
     * @param string $token The Bearer token to include in the request.
     * @return static Returns a new instance with the updated Authorization header.
     */
    public function withBearerToken(string $token): static;
     
    
    /**
     * Adds query parameters to the request URL.
     *
     * @param array $params An associative array of query parameters.
     * @return static Returns a new instance with the updated query parameters.
     */
    public function withQueryParams(array $params): static;
}