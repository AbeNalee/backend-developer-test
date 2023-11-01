## Backend Developer Test - Achievements and Badges

This Laravel application features a robust Achievement and Badge system to reward and recognize users' accomplishments as they interact with the platform.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Getting Started](#getting-started)
- [Testing](#testing)

## Overview

The Achievement and Badge system allows users to earn achievements and badges based on specific actions, such as watching lessons & writing comments. 
Achievements and badges are used to motivate and engage users, providing a gamified experience.

## Features

- **Achievements:**
    - Users can earn achievements for various actions.
    - Achievements can be unlocked progressively as users reach specific milestones.
    - Achievements are stored in the database for each user.

- **Badges:**
    - Users can earn badges based on the number of achievements they unlock.
    - Badges are awarded as users accumulate achievements, leading to different badge levels.
    - Badges are also stored in the database for each user.

- **Event Handling:**
    - The system dispatches events when achievements and badges are unlocked.

- **Testing:**
    - Comprehensive test suite covering various scenarios, including achievement and badge unlocking, event handling, and database records.

## Getting Started

To get started with this Achievement and Badge system, follow these steps:

1. Clone the repository to your local environment.

2. Install the necessary dependencies using Composer:

   ```bash
   composer install
   ```
3. Set up your database and configure the .env file with your database settings.

4. Run database migrations and seeders to create initial achievements and badges:
    ```bash
   php artisan migrate --seed
   ```
## Testing

The system includes a comprehensive test suite to ensure the correct functioning of the Achievement and Badge features.
You can run the tests using the following command:

```bash
php artisan test
```

The test suite covers various scenarios, including achievement and badge unlocking, event handling, database records, and more.
If a test fails, know that they all pass successfully on my computer 😂
