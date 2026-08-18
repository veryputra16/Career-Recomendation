# AI Career Recommendation System

# System Architecture

## 1. Purpose

This document defines the technical architecture of the AI Career Recommendation System.

The purpose of this document is to ensure that:

- The application has a clear structure.
- Each module has a defined responsibility.
- Business logic is separated from presentation logic.
- AI processing is isolated from the main application.
- The system remains maintainable and testable.
- Future development does not introduce unnecessary architectural changes.

This document must be followed together with:

docs/CLAUDE.md
docs/ROADMAP.md
docs/DATABASE.md
docs/AI.md
docs/API.md

```

---

# 2. System Overview

The application is a web-based AI Career Recommendation System.

The primary workflow is:


Student
   |
   v
React + Inertia
   |
   v
Laravel Application
   |
   +----------------------+
   |                      |
   v                      v
MySQL Database        File Storage
   |
   v
AI Processing
   |
   +-----------------------------+
   |             |               |
   v             v               v
Skill        Career          Job/Internship
Extraction   Recommendation   Matching
   |
   v
Analytics
   |
   v
PDF Report
```

---

# 3. High-Level Architecture

The application uses a layered architecture.

+------------------------------------------------+
| Presentation |
| |
| React + Inertia + Tailwind CSS |
+------------------------+-----------------------+
|
v
+------------------------------------------------+
| HTTP / Application |
| |
| Routes |
| Controllers |
| Form Requests |
| Middleware |
| Policies |
+------------------------+-----------------------+
|
v
+------------------------------------------------+
| Business Logic |
| |
| Services |
| Actions |
| Domain-specific logic |
+------------------------+-----------------------+
|
v
+------------------------------------------------+
| Data Access |
| |
| Eloquent Models |
| Repositories when necessary |
+------------------------+-----------------------+
|
v
+------------------------------------------------+
| Persistence |
| |
| MySQL |
| File Storage |
+------------------------------------------------+

                         +
                         |
                         v

+------------------------------------------------+
| External Services |
| |
| AI Provider |
| PDF Processing |
+------------------------------------------------+

```

---

# 4. Architectural Principles

The following principles are mandatory.

## 4.1 Separation of Concerns

Each layer must have a clear responsibility.

Do not mix:

* UI logic
* HTTP logic
* Business logic
* Database logic
* AI logic
* File processing

inside one class.

---

## 4.2 Thin Controllers

Controllers should not contain complex business logic.

Preferred:


Request
   ↓
Controller
   ↓
Service
   ↓
Model / Database
```

Avoid:

Request
↓
Controller
├── Validation
├── File Processing
├── AI Request
├── Database Queries
├── Recommendation Logic
└── PDF Generation

```

---

## 4.3 Service-Oriented Business Logic

Complex business processes should be implemented using Services.

Examples:


CVService
CVParserService
SkillExtractionService
SkillNormalizationService
SkillGapService
CareerRecommendationService
JobMatchingService
AnalyticsService
PDFReportService
```

Services should have a focused responsibility.

Avoid creating a single:

CareerService

```

that handles the entire application.

---

# 5. Laravel Application Structure

Recommended structure:


app/
├── Actions/
├── DTO/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Jobs/
├── Models/
├── Policies/
├── Services/
└── Support/
```

Additional folders should only be introduced when required.

Do not create architecture layers simply for the sake of having more folders.

---

# 6. Controller Layer

Controllers are responsible for HTTP concerns.

Responsibilities:

- Receive request.
- Authorize request.
- Validate request through Form Request.
- Call appropriate Service.
- Return Inertia response or API response.

Example:

CVController
|
v
CVService
|
v
CVParserService

```

Controllers should generally not:

* Parse PDF files.
* Call AI providers.
* Calculate matching scores.
* Generate PDF documents.
* Contain complex database queries.

---

# 7. Form Request Layer

Form Requests are responsible for input validation.

Examples:


StoreStudentProfileRequest
UpdateStudentProfileRequest
UploadCVRequest
StoreCareerRequest
StoreInternshipRequest
```

Validation should happen before business logic is executed.

---

# 8. Service Layer

The Service layer contains business processes.

Example:

CVService

```

Possible responsibilities:

* Store CV metadata.
* Manage current CV.
* Replace CV.
* Delete CV.
* Trigger processing.

Example:


SkillExtractionService
```

Responsibilities:

- Receive extracted CV text.
- Prepare AI input.
- Call AI service.
- Validate AI response.
- Normalize skills.
- Persist extracted skills.

---

# 9. AI Service Architecture

AI integration must be isolated.

The application should not depend directly on a specific AI provider throughout the codebase.

Preferred structure:

Application
|
v
CareerRecommendationService
|
v
AIService
|
v
AI Provider

```

For skill extraction:


CV Text
   |
   v
SkillExtractionService
   |
   v
AIService
   |
   v
AI Provider
   |
   v
Structured Response
```

This allows the AI provider to be changed without rewriting the entire application.

Detailed AI architecture is documented in:

docs/AI.md

```

---

# 10. CV Processing Architecture

CV processing is divided into several independent stages.


Upload CV
   |
   v
Validate File
   |
   v
Store File
   |
   v
Create CV Record
   |
   v
Extract PDF Text
   |
   v
Clean Text
   |
   v
Store Extracted Text
   |
   v
AI Skill Extraction
   |
   v
Normalize Skills
   |
   v
Store Student Skills
```

Each stage should be independently testable.

---

# 11. CV Upload Flow

The upload flow is:

Student
|
v
React Upload Form
|
v
Inertia Request
|
v
CVController
|
v
UploadCVRequest
|
v
CVService
|
+-------> File Storage
|
+-------> CV Database Record

```

The upload operation should not automatically contain the entire AI analysis pipeline unless explicitly designed as a queued workflow.

---

# 12. CV Parsing Flow

After the CV has been uploaded:


CV File
   |
   v
CVParserService
   |
   v
PDF Text Extraction
   |
   v
Text Cleaning
   |
   v
Extracted CV Text
```

The parser should not perform AI analysis.

Parsing and AI analysis are separate responsibilities.

---

# 13. Skill Extraction Flow

Extracted CV Text
|
v
SkillExtractionService
|
v
AIService
|
v
AI Provider
|
v
Structured JSON
|
v
Validate Response
|
v
Normalize Skills
|
v
Student Skills

```

Important:

AI output must be treated as untrusted external data.

Always validate the structure before storing it.

---

# 14. Skill Normalization

AI may return variations such as:


Laravel
laravel
Laravel Framework
PHP Laravel
```

The system should normalize these values against the internal skill dataset where possible.

Flow:

AI Skill
|
v
Normalize
|
v
Find Existing Skill
|
+------ Found ------> Use Existing Skill
|
+------ Not Found --> Handle Unknown Skill

```

The exact normalization strategy is defined in:


docs/AI.md
```

---

# 15. Skill Gap Architecture

Skill gap analysis should primarily be deterministic.

It should not depend entirely on AI.

Flow:

Student Skills
|
v
Career Requirements
|
v
Skill Gap Service
|
+----------------+
| |
v v
Existing Skills Missing Skills
|
v
Coverage Score

```

Example:


Career:
Backend Developer

Required:

PHP
Laravel
MySQL
Docker
Redis

Student:

PHP
Laravel
MySQL

Result:

Existing:
PHP
Laravel
MySQL

Missing:
Docker
Redis

Coverage:
60%
```

---

# 16. Career Recommendation Architecture

Career recommendation combines deterministic application data with AI-generated reasoning.

Preferred flow:

Student Profile
|
v
Student Skills
|
v
Skill Gap Analysis
|
v
Career Candidates
|
v
CareerRecommendationService
|
v
AIService
|
v
AI Recommendation
|
v
Validation
|
v
Store Recommendation

```

AI should not be given unlimited authority to invent careers or requirements.

Career candidates should be based on controlled application data where possible.

---

# 17. Career Recommendation Result

A recommendation should contain structured information such as:


Career
Matching Score
Reason
Existing Skills
Missing Skills
Improvement Suggestions
```

Example:

Career:
Backend Developer

Matching:
92%

Existing Skills:
PHP
Laravel
MySQL
REST API

Missing Skills:
Docker
Redis

Reason:
The student has strong backend development fundamentals
and relevant Laravel experience.

Suggested Improvement:
Learn Docker and Redis.

```

---

# 18. Internship / Job Matching Architecture

Job matching should not rely entirely on generative AI.

Recommended architecture:


Student Skills
       |
       v
Matching Engine
       |
       v
Candidate Jobs
       |
       v
Matching Score
       |
       v
AI Explanation (Optional)
       |
       v
Final Recommendation
```

The matching engine should use structured job data.

AI may explain the result, but must not invent job information.

---

# 19. Matching Engine

The matching engine should be deterministic where possible.

Example:

Required Skills:
Laravel
PHP
MySQL
Docker

Student Skills:
Laravel
PHP
MySQL

Matched:
3 / 4

Score:
75%

```

The exact scoring methodology must be documented before implementation.

---

# 20. Internship Recommendation

Internship recommendations should contain:


Position
Organization
Location
Matching Score
Matched Skills
Missing Skills
Explanation
Source
```

The system should preserve the original source of job/internship information.

---

# 21. Student Dashboard Architecture

The Student Dashboard consumes existing application data.

It should not contain business logic for calculating recommendations.

Preferred:

DashboardController
|
v
DashboardService
|
+---- Student Profile
+---- Skills
+---- Recommendations
+---- Skill Gap
+---- Internship Matching
|
v
Inertia Response
|
v
React Dashboard

```

---

# 22. Admin Dashboard Architecture

Admin analytics should aggregate database data.

Preferred:


Admin Dashboard
      |
      v
AnalyticsService
      |
      +---- Student Statistics
      +---- Skill Statistics
      +---- Career Statistics
      +---- Matching Statistics
      |
      v
Inertia
      |
      v
React Charts
```

Charts should only visualize data received from the backend.

Do not move database/business calculations into React.

---

# 23. Analytics Architecture

Analytics should use database aggregation where possible.

Examples:

COUNT()
GROUP BY
AVG()
SUM()

```

Avoid loading thousands of records into PHP only to calculate simple statistics.

For expensive analytics:


Database
   |
   v
Aggregation
   |
   v
AnalyticsService
   |
   v
Dashboard
```

Caching may be introduced later if necessary.

---

# 24. PDF Report Architecture

PDF generation should be isolated.

Preferred:

Student
|
v
Report Request
|
v
PDFReportService
|
+---- Student Data
+---- Skill Data
+---- Career Data
+---- Matching Data
|
v
PDF Generator
|
v
PDF File

```

The controller should not directly construct the entire PDF.

---

# 25. Queue Architecture

Long-running processes may use Laravel queues.

Candidates:


CV Text Extraction
AI Skill Extraction
Career Recommendation
PDF Generation
```

Possible flow:

Upload CV
|
v
Dispatch Job
|
v
Queue
|
v
CV Processing
|
v
AI Processing
|
v
Database

```

Queue usage should be introduced when the implementation requires it.

Do not introduce unnecessary queue complexity during the initial foundation phase.

---

# 26. Frontend Architecture

React is responsible for presentation and user interaction.

Recommended structure:


resources/js/
├── Components/
├── Layouts/
├── Pages/
├── Hooks/
├── Lib/
└── Utils/
```

---

# 27. React Components

Components should be reusable.

Examples:

Button
Input
Modal
Card
Badge
Table
FileUploader
StatCard
ChartCard
EmptyState
LoadingState

```

Feature-specific components may also be created.

Examples:


CVUploader
SkillList
CareerRecommendationCard
SkillGapCard
InternshipCard
```

---

# 28. React Pages

Pages represent application screens.

Examples:

Pages/
├── Auth/
├── Student/
│ ├── Dashboard.jsx
│ ├── Profile.jsx
│ ├── CV/
│ ├── Skills/
│ ├── Careers/
│ ├── Internships/
│ └── Reports/
└── Admin/
├── Dashboard.jsx
├── Students/
├── Skills/
├── Careers/
└── Internships/

```

Actual structure may evolve according to implementation.

---

# 29. Inertia Architecture

Inertia connects Laravel backend responses with React pages.

Preferred flow:


Browser
   |
   v
React
   |
   v
Inertia Request
   |
   v
Laravel Route
   |
   v
Controller
   |
   v
Service
   |
   v
Database
   |
   v
Inertia Response
   |
   v
React Page
```

Do not duplicate backend business logic inside React.

---

# 30. API Architecture

If APIs are required, they should be separated from Inertia web routes.

Recommended:

routes/
├── web.php
└── api.php

```

API Controllers should use the same underlying Services as web controllers whenever possible.

Example:


API Controller
      |
      v
CareerRecommendationService
      |
      v
Database / AI
```

Avoid duplicating business logic between API and web controllers.

---

# 31. Data Ownership

Each module should have clear ownership.

Example:

Student Module
owns student profile

CV Module
owns CV files and processing state

Skill Module
owns normalized skills

Career Module
owns career definitions

Recommendation Module
owns generated recommendations

Job Module
owns job/internship data

Analytics Module
reads aggregated data

Report Module
generates reports

```

Modules should communicate through defined services and relationships.

---

# 32. Database Relationship Overview

High-level relationship:


User
 |
 +---- Student
          |
          +---- CV
          |
          +---- Student Skills
          |
          +---- Career Recommendations
          |
          +---- Job Matches
          |
          +---- Reports
```

Master data:

Skill
|
+---- Student Skills
|
+---- Career Skills
|
+---- Job Skills

```

Career:


Career
 |
 +---- Career Skills
 |
 +---- Career Recommendations
```

Job:

Job / Internship
|
+---- Job Skills
|
+---- Job Matches

```

The exact schema must be defined in:


docs/DATABASE.md
```

---

# 33. Authentication Architecture

Authentication flow:

User
|
v
Login
|
v
Authentication
|
v
User
|
+---- Admin
|
+---- Student

```

Authorization must happen on the backend.

Frontend role checks are only for UI presentation.

---

# 34. Security Boundaries

Important security boundaries:


Browser
   |
   | Authentication
   v
Laravel
   |
   | Authorization
   v
Business Logic
   |
   | Validation
   v
Database / Storage / External Services
```

Never trust:

- Frontend input
- Uploaded filenames
- AI output
- Client-side role information
- External API responses

All important data must be validated server-side.

---

# 35. External Service Boundaries

External services should be accessed through dedicated classes.

Examples:

AIService
PDFService
StorageService

```

Do not scatter external API calls throughout the application.

This makes:

* Testing easier.
* Provider changes easier.
* Error handling centralized.
* API credentials easier to manage.

---

# 36. Error Handling Architecture

Expected errors should be handled at the appropriate layer.

Example:


AI API Failure
      |
      v
AIService
      |
      v
Exception / Result
      |
      v
Business Service
      |
      v
Controller
      |
      v
User-friendly response
```

Do not expose technical exception details to users.

---

# 37. Logging Architecture

Log important technical events.

Examples:

CV processing started
CV processing failed
AI request failed
AI response validation failed
PDF generation failed

```

Never log:

* API keys
* Passwords
* Sensitive authentication tokens
* Full CV contents unnecessarily
* Sensitive personal information unnecessarily

---

# 38. Testing Architecture

Testing should follow application boundaries.


Unit Tests
    |
    +---- Services
    +---- Matching calculations
    +---- Skill normalization
    +---- Score calculations

Feature Tests
    |
    +---- Authentication
    +---- Authorization
    +---- CV upload
    +---- Recommendation workflow

Integration Tests
    |
    +---- AI integration where appropriate
    +---- PDF generation
```

AI API calls should generally be mocked during automated tests.

---

# 39. AI Cost Control

AI requests are potentially expensive.

The architecture should minimize unnecessary AI calls.

Preferred:

CV
|
v
Extract Once
|
v
Store Text
|
v
Analyze When Needed
|
v
Store Result

```

Do not repeatedly send the same CV to the AI unless a new analysis is explicitly requested.

AI-generated results should be persisted when appropriate.

---

# 40. Reprocessing Strategy

CV processing should support controlled reprocessing.

Example:


CV uploaded
     |
     v
Processed
     |
     v
Analysis stored
```

If re-analysis is requested:

Existing CV
|
v
New Analysis Request
|
v
Process Again
|
v
New Analysis Version

```

Do not overwrite historical analysis unnecessarily.

---

# 41. Versioning of AI Analysis

AI analysis may change when:

* AI model changes.
* Prompt changes.
* Skill dataset changes.
* Career dataset changes.

Where practical, store metadata such as:


AI Provider
AI Model
Prompt Version
Analysis Timestamp
```

This allows historical results to be understood and compared.

---

# 42. Recommended Directory Structure

A recommended high-level structure:

app/
├── Actions/
├── DTO/
├── Http/
│ ├── Controllers/
│ ├── Requests/
│ └── Resources/
├── Jobs/
├── Models/
├── Policies/
├── Services/
└── Support/

database/
├── factories/
├── migrations/
└── seeders/

resources/
└── js/
├── Components/
├── Hooks/
├── Layouts/
├── Pages/
├── Lib/
└── Utils/

routes/
├── api.php
└── web.php

docs/
├── CLAUDE.md
├── ROADMAP.md
├── ARCHITECTURE.md
├── DATABASE.md
├── AI.md
└── API.md

storage/

```

Do not create every directory immediately.

Create directories when they are actually required.

---

# 43. Module Dependency Rules

Dependencies should generally flow in one direction:


Presentation
     ↓
Application
     ↓
Business Logic
     ↓
Data Access
     ↓
Infrastructure
```

Avoid circular dependencies.

Examples of bad architecture:

React
↓
AI Service

```

or:


Model
 ↓
React
```

or:

Controller
↓
AI Provider SDK

```

Preferred:


React
 ↓
Controller
 ↓
Service
 ↓
AI Service
 ↓
AI Provider
```

---

# 44. What Should NOT Be Done

Do not:

- Put AI API calls in React.
- Put AI API calls directly in controllers.
- Put complex business logic in models.
- Put database queries throughout React.
- Put database calculations inside frontend charts.
- Store API keys in source code.
- Trust AI output without validation.
- Allow AI to invent job data.
- Mix CV parsing and AI analysis into one giant class.
- Create one service responsible for the entire system.
- Refactor unrelated modules during feature development.
- Add unnecessary dependencies.
- Build future roadmap features without instruction.

---

# 45. Architecture Evolution

This architecture is allowed to evolve.

However, architectural changes should only be made when:

- There is a clear technical reason.
- The current architecture cannot reasonably support the requirement.
- The change improves maintainability or scalability.

When a significant architecture change is necessary:

1. Explain the reason.
2. Identify affected modules.
3. Update `ARCHITECTURE.md`.
4. Update related documentation.
5. Then implement the change.

Do not silently change the architecture.

---

# 46. Final Architecture Principle

The system should remain:

Simple +
Modular +
Testable +
Secure +
Maintainable

```

The primary development principle is:


Small Feature
     ↓
Clear Responsibility
     ↓
Reusable Service
     ↓
Validated Data
     ↓
Test
     ↓
Document
```

Do not optimize for the amount of code written.

Optimize for a system that can be understood, tested, maintained, and extended.
