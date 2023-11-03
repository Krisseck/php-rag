# PHP-RAG

A "chatbot" built with PHP, Solr (or other DB backend) and LLM of choice. Proof of concept mostly.

## Installation

This project uses [DDEV](https://ddev.com/) to manage the environment.

After installing DDEV, run:

```
ddev start
ddev composer i
cp env.example .env
```

If you are not using DDEV, you will need:

- PHP 8.1 with at least curl extension
- Composer
- Solr server
- Access to a LLM service

## Configuration

`.env` / `env.example` has the following configuration options:

- SOLR_HOST - Hostname of Solr server 
- SOLR_PORT - Port of Solr server 
- SOLR_CORE - Name of the core of the Solr server 
- CONTEXT_WORD_COUNT - Maximum word count to be included in LLM context (note: it's words, not tokens!)
- REPLICATE_API_KEY - Replicate API key (if using Replicate LLM)
- REPLICATE_MODEL_VERSION - Hash of the model version used in Replicate (if using Replicate LLM)
- KOBOLDAI_HORDE_API_KEY - KoboldAI Horde API key (if using KoboldAI Horde LLM)

### Backend (database)

Currently only supports Solr server. But more backends (like SQL or Elasticsearch) can be done easily, just implement the `Krisseck\PhpRag\Backend\BackendInterface`.

### LLM

Supports both Replicate and KoboldAI LLM services. Replicate is easier to use, you can get a free API key on https://replicate.com/. 

For Replicate, you need to provide the hash of the model version you will be using. You can get the hash from "Versions" tab on a model's page.

## Demo

There's an example script in `examples` folder. To run it with DDEV:

```
ddev start
ddev ssh
cd examples
php chicago-prompt.php
```

Note that fetching the output can take a while on free APIs, so be patient. 🙂

