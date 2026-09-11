# Architecture and Design Decisions

## Overview

The application is a Laravel web application for managing and displaying user notifications.

The application uses Laravel Livewire for the interactive pages. The main structure is:

- Laravel migrations define the database structure.
- Eloquent models represent the database tables and relationships.
- Livewire components handle the application pages and user interactions.
- `NotificationService` contains the shared notification logic.
- Laravel authentication protects the authenticated pages.

The ER diagram of the database is available in [database-erd.pdf](database-erd.pdf).

## Application Structure

The application follows a simple Laravel structure without adding unnecessary layers.

### Livewire components

Livewire components are used for the application's pages, including:

- Home
- Users
- Notifications
- Notification Management
- Post Notification
- Settings
- Login

The components handle displaying data, user input, filtering, and page interactions.

No controllers were added because the application does not require additional controller logic. Using Livewire components directly keeps the implementation simple and fits the interactive nature of the application.

### Models

The main Eloquent models are:

- `User`
- `Notification`
- `NotificationRead`

The models define the relationships between the database tables and provide access to the application data.

### NotificationService

Notification-related logic that is shared between different parts of the application is kept in `NotificationService`.

For example, the service is responsible for:

- Finding notifications available to a user, including global notifications.
- Excluding expired notifications.
- Finding unread notifications.
- Calculating the unread notification count.
- Marking notifications as read.

Keeping this logic in one service avoids duplicating the same notification rules in different Livewire components.

## Database Design

The application uses three main tables:

- `users`
- `notifications`
- `notification_reads`

### Users and notifications

A notification can be targeted at one specific user or sent to all users.

This is represented by the nullable `notifications.user_id` field:

- A user ID means the notification is targeted at that user.
- `NULL` means the notification is global and available to all users.

This allows both types of notifications to use the same table.

### Notification read tracking

Read status is stored separately in `notification_reads`.

A record connects a user with a notification and stores when it was read.

The database has a unique constraint on:

`notification_id, user_id`

This ensures that the same user cannot have multiple read records for the same notification.

The separate read table also allows global notifications to have different read states for different users.

### Notification expiration

Each notification has an `expires_at` timestamp.

Expired notifications are excluded by `NotificationService`, even if the user has not read them yet.

This keeps expiration logic consistent wherever notifications are displayed or counted.

## Notification Management

The notification management page provides filters for:

- Notification type
- Recipient
- Specific user

The recipient filter first distinguishes between global and specific-user notifications. When a specific user is selected, the user filter is applied to the same notification query.

The filtering is performed at the database query level rather than loading all notifications and filtering them in the browser.

Notifications are loaded with their related user using Eloquent eager loading to avoid unnecessary database queries when displaying the recipient.

## User Settings

Users can update:

- Notification preference
- Email address
- Phone number

The notification preference is stored in `users.notifications_enabled` and defaults to `true`.

Phone numbers are validated using `giggsey/libphonenumber-for-php`.

The library checks whether the number is valid and classified as a mobile number. It does not verify ownership of the number and does not require an external API key or paid service.

## Authentication and Impersonation

The application uses Laravel's standard authentication system.

Authenticated users can access the application pages protected by the `auth` middleware.

The Users page allows a user to select another user and view the application as that user. This is implemented as a simple impersonation mechanism for the proof of concept and does not introduce a separate role or permission system.

No administrator role was added because the assignment does not require role-based authorization.