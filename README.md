# Document Workflow API

A RESTful API built with CodeIgniter 4 for managing document workflow processes. This application implements a task-based document submission system with role-based access control.

## ️ Architecture

This project follows a **microservices architecture** with clear separation between frontend and backend services. The API implements several design patterns for maintainable and scalable code:

- **Service Layer Pattern** - Business logic separation
- **Repository Pattern** - Data access abstraction
- **JWT Authentication** - Stateless authentication
- **Role-Based Access Control (RBAC)** - Permission management
- **RESTful API Design** - Standard HTTP methods and status codes

## Features

### Authentication & Authorization
- JWT-based authentication
- Role-based access control (Administrator/Collaborator)
- Protected endpoints with middleware validation

### Task Management (Administrator Only)
- Create new document submission tasks
- Assign tasks to collaborators
- View all tasks in the system
- Task type categorization

### Task Execution (Collaborator Only)
- View assigned tasks
- Submit task execution with description
- File upload to AWS S3 (emulated)
- Task completion tracking

## Tech Stack

- **Backend:** CodeIgniter 4 (PHP 8.1)
- **Database:** MariaDB 10.4
- **File Storage:** AWS S3 (S3 Ninja emulator)
- **Web Server:** Apache (via Docker)
- **Containerization:** Docker & Docker Compose
- **Authentication:** JWT tokens

## Prerequisites

- Docker & Docker Compose
- Composer (for local development)
- Git

## Getting Started

### 1. Clone the Repository
```bash
git clone https://github.com/franciscoDX/document_workflow_api.git
cd document_workflow_api
```

### 2. Environment Setup
```bash
# Copy environment file
cp env .env

```

### 3. Start Services
```bash
# Build and start all services
docker-compose up -d

# Check services status
docker-compose ps
```

### 4. Database Setup
```bash
# Run migrations
docker-compose exec backend php spark migrate

```
