# 🐱 Moti Meow - Gamified Wellness App (Adventure Module)

![PHP](https://img.shields.io/badge/Backend-PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Azure](https://img.shields.io/badge/AI-Azure%20OpenAI-0078D4?style=for-the-badge&logo=microsoftazure&logoColor=white)
![Status](https://img.shields.io/badge/Status-Showcase%20Clone-orange?style=for-the-badge)

## ⚠️ Project Disclaimer & Credits
**This repository is a showcase clone of the group project "Moti Meow".**

* **Original Project:** Moti Meow
* **Original Author:** ThengFeng
* **Team Contributors:** ThengFeng, LHK21, jslim0727, lowjinghong29
* **My Role:** **Adventure System Developer**

> *This code is hosted here for portfolio demonstration purposes to highlight my specific contributions to the Adventure Module logic, Stamina System, and AI integration.*

---

## 📖 Project Overview
**Moti Meow** is a web-based application that encourages users to maintain healthy habits through interactive activities with a virtual cat. Users can complete exercise routines to maintain their pet's health.

**My Module (The Adventure System):**
I was responsible for designing and implementing the **Gamification Engine**, specifically the Adventure Mode. This system manages user stamina, integrates with Azure OpenAI to generate unique stories, and handles the "Risk vs. Reward" gameplay loop.

---

## 🛠️ My Technical Contributions
I architected the **Adventure Module** using a 3-Tier Layered Architecture (Controller-Model-View).

### 1. Stamina Management Engine
I built a server-side stamina system with time-based auto-recovery.
* **Logic:** Implemented a 5-tier energy system.
* **Auto-Recovery:** `1 Point` recovers every `120 Minutes`.
* **Math:** Calculated via `lastEnergyUpdate` timestamp in the database to ensure accuracy even when the user is offline.
* **Consumables:** Created logic for "Recovery Items" to instantly refill stamina via the Inventory API.

### 2. AI Story Generation (Azure OpenAI)
Integrated Generative AI to create infinite, unique gameplay narratives.
* **Prompt Engineering:** Structured prompts to ensure stories are first-person, fit the "cat" persona, and match the visual background.
* **Optimization:** Implemented database caching to prevent redundant API calls and reduce costs.
* **Flow:** `User Starts Adventure` → `Check Cache/Call API` → `Stream Story` → `Save to DB`.

### 3. Gameplay Loop & Rewards
* **Timer System:** Implemented a 100-second synchronization between Client (JS Timer) and Server (Validation).
* **Reward Logic:** Upon completion, the system checks the user's inventory to prevent duplicate drops. If all items are collected, it falls back to granting consumable items.

---

## 💻 Tech Stack

| Layer | Technology |
| :--- | :--- |
| **Frontend** | HTML5, CSS3, JavaScript (jQuery) |
| **Backend** | Native PHP 7.4+ |
| **Database** | MySQL / MariaDB |
| **AI / API** | Azure OpenAI Service |
| **Server** | Apache / Nginx |

---

## 📂 Database Schema (Adventure Module)

I designed the following schema to support the adventure features:

* **`user`**: Stores user core stats and `lastEnergyUpdate` timestamp.
* **`adventure`**: Records history (`adventureID`, `storyContent`, `dateTime`).
* **`adventure_background`**: Stores dynamic assets for story themes.
* **`userinventorylist`**: Cross-referenced during the reward generation phase.

---

## 🚀 Installation & Setup

If you wish to run this module locally:

### 1. Prerequisites
* PHP 7.4+
* MySQL 5.7+
* Apache/Nginx (with `mod_rewrite` enabled)

### 2. Environment Configuration
Create a `.env` file in the project root with your database and Azure credentials:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=moti_meow_db

AZURE_OPENAI_KEY=your_key_here
AZURE_OPENAI_ENDPOINT=your_endpoint_here
