## Introduction

The **IML Appmonitor** is a tool for internal usage. For developers, Devops, Sysadmins. It offers details that shouldn't be for the public.

To show the health status for a public audience you need to hide all technical details. For security reasons on one hand and to not confuse the visitors with unknown buzzwords on the other.

The API client helps you to fetch API data. It handles the encryption for HMAC authentication. The response is JSON.

**Links**:

* IML Appmonitor <https://github.com/iml-it/appmonitor>
* Description of API <https://os-docs.iml.unibe.ch/appmonitor/Server/API.html>

```mermaid
---
config:
  look: handDrawn
  theme: base
---
flowchart LR

    subgraph internal view with technical details
        Appmonitor@{ shape: processes, label: "IML Appmonitor" }
        API
    end

    subgraph public Healthmonitor
        APIclient@{ label: "API client" }
    end

    App1([App1]) e1@--> Appmonitor
    App2([App2]) e2@--> Appmonitor
    App3([App3]) e3@--> Appmonitor
    App4([AppN]) e4@--> Appmonitor

    Appmonitor --- API

    API e5@-- JSON --> APIclient
    APIclient e5@-- Get apps by tag --> API

    e1@{animation: slow}
    e2@{animation: slow}
    e3@{animation: slow}
    e4@{animation: slow}
    e5@{animation: slow}
```
