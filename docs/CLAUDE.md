# CLAUDE.md

# AI Career Recommendation System

## 1. Project Overview

This project is an AI-powered Career Recommendation System for university students.

The system analyzes student CVs, extracts skills automatically, recommends suitable career paths, recommends internship opportunities, provides analytics, and generates career reports in PDF format.

### Main Users

- Student
- Admin

### Main Features

- Student/Admin authentication
- Student profile
- CV upload
- CV text extraction
- Automatic skill extraction
- Skill gap analysis
- AI career recommendation
- Internship recommendation
- Job matching
- Student dashboard
- Admin analytics dashboard
- Career statistics
- Skill statistics
- PDF career report

---

# 2. Technology Stack

## Backend

- Laravel 12
- PHP 8.2+
- MySQL 8+
- Laravel Eloquent ORM

## Frontend

- React
- Inertia.js
- JavaScript
- Tailwind CSS

## Authentication

- Laravel authentication
- Role-based authorization
- Policies / Gates where appropriate

## AI

- AI API integration through a dedicated service layer
- API provider must be configurable through `.env`
- Never hardcode API keys

## PDF

- DomPDF or another Laravel-compatible PDF library

## Charts

- Chart.js or another React-compatible chart library

---

# 3. Development Philosophy

This project MUST be developed incrementally.

Do not attempt to build the entire application in one task.

Each development task should focus on one clearly defined feature or technical objective.

The development process must prioritize:

- Stability
- Maintainability
- Simplicity
- Security
- Reusability
- Testability
- Clean architecture

Do not over-engineer the application.

Prefer simple solutions that are appropriate for the current project requirements.

---

# 4. Task Scope Rules

When receiving a development request:

1. Work ONLY on the requested task.
2. Do not implement future features automatically.
3. Do not modify unrelated modules.
4. Do not refactor unrelated code.
5. Do not redesign the architecture unless explicitly requested.
6. Do not add unnecessary dependencies.
7. Reuse existing components and services whenever possible.
8. Preserve existing working functionality.
9. If a dependency or architectural change is genuinely required, explain it before making unnecessary changes.
10. Stop when the requested task is complete.

### Important

Never interpret a feature request as permission to implement the entire related module.

Example:

If the task is:

> Implement CV upload.

Do NOT automatically implement:

- PDF text extraction
- AI skill extraction
- Career recommendation
- Job matching
- Analytics

Those are separate tasks.

---

# 5. Development Workflow

Development must follow the project roadmap.

The roadmap is stored in:

`docs/ROADMAP.md`

Before starting a task:

1. Read this `CLAUDE.md`.
2. Read the relevant section of `docs/ROADMAP.md`.
3. Inspect only files related to the requested task.
4. Understand the existing implementation.
5. Identify files that need to be created or modified.
6. Implement only the requested scope.
7. Run appropriate tests or validation.
8. Report the result.
9. Stop and wait for the next task.

Do not scan the entire project unless explicitly necessary.

---

# 6. Context and Token Efficiency

This project is developed using AI-assisted development.

Context and token usage must be minimized.

Therefore:

- Do not analyze the entire codebase unnecessarily.
- Do not read unrelated files.
- Do not repeat previously explained information.
- Do not reproduce entire files when only a small section needs modification.
- Do not generate unnecessary boilerplate.
- Do not create unnecessary documentation.
- Do not explain basic concepts unless requested.
- Prefer concise implementation summaries.
- Focus the context on the current task.

When inspecting the project, prioritize:

1. Files directly related to the task.
2. Related models/controllers/services/components.
3. Relevant migrations.
4. Relevant routes.
5. Relevant tests.

---

# 7. Architecture Principles

Use a clean and maintainable Laravel architecture.

Preferred flow:

Request

↓

Controller

↓

Form Request / Validation

↓

Service

↓

Repository / Eloquent

↓

Database

Business logic must not be placed directly inside controllers.

Controllers should remain thin.

---

# 8. Backend Rules

## Controllers

Controllers should primarily handle:

- Request handling
- Authorization
- Calling services
- Returning Inertia responses or API responses

Avoid large controllers.

Do not place complex business logic inside controllers.

---

## Form Requests

Use Laravel Form Request classes for complex validation.

Avoid large validation blocks directly inside controllers.

---

## Services

Use Services for business logic.

Examples:

- `CVService`
- `CVParserService`
- `SkillExtractionService`
- `CareerRecommendationService`
- `JobMatchingService`
- `AnalyticsService`
- `PDFReportService`

Do not create services unnecessarily.

Create a service when the logic is complex, reusable, or represents a clear business process.

---

## Models

Use Eloquent relationships properly.

Prefer:

- Relationships
- Scopes
- Accessors where appropriate
- Casts
- Model methods for small model-specific behavior

Avoid putting large business workflows inside models.

---

## Database

Use Laravel migrations for all schema changes.

Use:

- Foreign keys
- Appropriate indexes
- Proper nullable definitions
- Appropriate data types
- Timestamps

Follow normalized relational database design.

Avoid duplicate data unless there is a clear reason.

---

# 9. Frontend Rules

Use React with Inertia.js.

Prefer:

- Functional components
- Reusable components
- Reusable layouts
- Custom hooks where useful
- Small focused components

Avoid:

- Huge JSX files
- Repeated UI code
- Unnecessary state
- Business logic inside presentation components

Keep business logic on the backend whenever appropriate.

---

# 10. UI/UX Rules

The application should have a modern, clean, professional academic/career platform appearance.

Prioritize:

- Responsive design
- Clear hierarchy
- Consistent spacing
- Consistent typography
- Accessible forms
- Clear validation messages
- Loading states
- Empty states
- Error states
- Success feedback

Use reusable UI components instead of repeatedly creating similar interfaces.

Do not introduce a new design system or component library unless explicitly requested.

---

# 11. Authentication and Authorization

The application has two main roles:

- `admin`
- `student`

Authorization must be enforced on the backend.

Never rely only on frontend role checks.

Use:

- Middleware
- Policies
- Gates

where appropriate.

A student must never be able to access admin-only functionality by manipulating frontend requests.

---

# 12. CV Upload Rules

CV uploads must be validated.

Expected format:

- PDF

The maximum file size should be configurable.

Never trust the original filename.

Use Laravel Storage.

Do not expose internal storage paths.

CV files may contain sensitive personal information, therefore:

- Validate access permissions.
- Do not expose CV files publicly unless explicitly required.
- Do not log CV contents.
- Do not expose extracted CV text unnecessarily.

---

# 13. CV Processing Architecture

CV processing should follow a clear pipeline:

CV Upload

↓

PDF Validation

↓

File Storage

↓

Text Extraction

↓

Text Cleaning

↓

Skill Extraction

↓

Skill Normalization

↓

Career Recommendation

↓

Skill Gap Analysis

↓

Job / Internship Matching

The pipeline should be modular.

Do not combine all processing logic into one controller or one class.

Long-running AI or document-processing operations should use queues when appropriate.

---

# 14. AI Integration Rules

AI functionality must be isolated from the rest of the application.

Never call an AI API directly from:

- Controllers
- React components
- Models

Use dedicated service classes.

Example:

`AIService`

or specialized services such as:

`SkillExtractionService`

`CareerRecommendationService`

`JobMatchingService`

---

## AI Configuration

Never hardcode:

- API keys
- API secrets
- Provider credentials

Use `.env`.

Example:

```env
AI_PROVIDER=
AI_API_KEY=
AI_MODEL=
```

The application should be designed so that the AI provider can be changed with minimal code changes.

---

# 15. AI Output Rules

AI responses should use structured output whenever possible.

Prefer JSON over unstructured text.

Example:

```json
{
  "skills": [
    {
      "name": "Laravel",
      "confidence": 0.95
    },
    {
      "name": "React",
      "confidence": 0.9
    }
  ]
}
```

Always validate AI-generated data before storing it in the database.

Never blindly trust AI output.

Handle:

- Invalid JSON
- Missing fields
- Unexpected values
- API timeout
- API errors
- Rate limits
- Empty responses

AI output must never directly determine authorization or security decisions.

---

# 16. Skill Management

Skills should be normalized.

Avoid duplicate skills such as:

- `Laravel`
- `laravel`
- `Laravel Framework`

unless they intentionally represent different entities.

Use a centralized skill structure.

Student skills should be related to the student through a proper relational design.

---

# 17. Career Recommendation

Career recommendation should consider:

- Student skills
- Skill proficiency/confidence
- Education
- Experience
- Relevant interests when available
- Career requirements

Recommendations should provide explainable results.

A recommendation should ideally contain:

- Career
- Matching score
- Reason
- Existing relevant skills
- Missing skills
- Suggested improvement

Do not present AI recommendations as guaranteed career outcomes.

---

# 18. Job and Internship Matching

Job/internship matching should use a controlled dataset or validated external source.

Do not allow AI to invent job vacancies.

AI may be used to explain or improve matching, but actual job/internship data must come from a trusted dataset or source.

A matching result should include:

- Position
- Organization/company
- Matching score
- Matching skills
- Missing skills
- Reason

---

# 19. Dashboard Analytics

### Student Dashboard

Possible information:

- CV status
- Extracted skills
- Career readiness score
- Career recommendations
- Skill gaps
- Internship recommendations
- Analysis history

### Admin Dashboard

Possible information:

- Total students
- Total CVs
- Total analyzed CVs
- Most common skills
- Career recommendation distribution
- Skill distribution
- Internship/job matching statistics

Use charts only when they improve understanding.

Avoid unnecessary visualizations.

---

# 20. PDF Report

PDF reports may contain:

- Student profile
- CV analysis summary
- Extracted skills
- Skill gap
- Career recommendations
- Internship recommendations
- Career readiness score
- Relevant charts/statistics

PDF generation should be handled by a dedicated service.

Do not place PDF generation logic inside controllers.

---

# 21. Error Handling

Handle errors gracefully.

Users should receive understandable messages.

Do not expose:

- Stack traces
- Database errors
- API keys
- Internal paths
- Raw AI errors

Use Laravel logging for technical errors.

---

# 22. Security

Always consider:

- Authentication
- Authorization
- CSRF protection
- Request validation
- File validation
- Rate limiting
- SQL injection prevention
- XSS prevention
- Secure file access
- Environment variable protection

Never commit `.env`.

Never expose secrets in frontend code.

---

# 23. Performance

Avoid unnecessary database queries.

Use eager loading when appropriate.

Watch for N+1 queries.

Use pagination for large datasets.

Use queues for long-running tasks such as:

- CV parsing
- AI analysis
- PDF generation

Cache expensive analytics where appropriate.

Do not optimize prematurely.

---

# 24. Testing

Important business logic must have tests.

Prioritize testing for:

- Authentication
- Authorization
- CV upload
- CV validation
- PDF extraction
- Skill extraction
- Career recommendation
- Job matching
- PDF generation

Use:

- Feature tests
- Unit tests
- Service tests

When adding a significant feature, add or update the relevant tests.

---

# 25. Coding Standards

Follow:

- PSR-12
- Laravel conventions
- React conventions
- Meaningful naming
- Single Responsibility Principle
- DRY
- KISS

Use clear names.

Avoid unnecessary abbreviations.

Prefer readable code over clever code.

---

# 26. File Modification Rules

Before modifying a file:

1. Inspect the existing implementation.
2. Understand its purpose.
3. Make the smallest necessary change.
4. Preserve existing behavior.

Do not rewrite an entire file when a targeted modification is sufficient.

Do not modify files outside the current task scope unless required.

If unrelated issues are discovered, report them but do not fix them automatically.

---

# 27. Dependency Rules

Do not install a new package without a clear reason.

Before adding a dependency:

1. Check whether Laravel or an existing package already provides the functionality.
2. Determine whether the dependency is necessary.
3. Consider maintenance and compatibility.
4. Explain why it is needed.

Avoid unnecessary dependencies.

---

# 28. Git Rules

Use small, meaningful commits.

Recommended format:

```text
feat: add cv upload module

fix: resolve cv validation issue

refactor: extract skill logic into service

test: add career recommendation tests

docs: update database documentation

chore: update dependencies
```

Do not mix unrelated features into one commit.

---

# 29. Task Completion Format

After completing a task, provide only a concise summary containing:

### Completed

- What was implemented.

### Modified Files

- Files created or modified.

### Testing

- What was tested.
- Any command that should be run manually.

### Git Commit

Suggest one appropriate commit message.

Then STOP.

Do not automatically continue to the next roadmap task.

---

# 30. Important Development Rule

The project roadmap is intentionally divided into small tasks to reduce context usage and AI development cost.

Claude MUST respect this structure.

Do not:

- Build multiple major features at once.
- Continue automatically to the next task.
- Refactor the entire project.
- Rewrite unrelated modules.
- Introduce unnecessary architecture.
- Generate unnecessary documentation.

Focus on one task at a time.

---

# 31. Documentation

Project documentation is stored in:

```text
docs/
├── CLAUDE.md
├── ROADMAP.md
├── ARCHITECTURE.md
├── DATABASE.md
├── AI.md
└── API.md
```

Read only the documentation relevant to the current task.

### Documentation Responsibilities

`CLAUDE.md`

- AI development rules
- Coding rules
- Development workflow

`ROADMAP.md`

- Development phases
- Task sequence
- Task status

`ARCHITECTURE.md`

- System architecture
- Application flow
- Module responsibilities

`DATABASE.md`

- Database structure
- Tables
- Relationships
- Constraints

`AI.md`

- AI architecture
- Prompt structure
- AI input/output
- AI processing flow

`API.md`

- API endpoints
- Request format
- Response format

Do not duplicate the same information across multiple documentation files.

---

# 32. Final Rule

When in doubt:

1. Keep the implementation simple.
2. Follow the existing architecture.
3. Respect the current task scope.
4. Avoid unrelated changes.
5. Ask for clarification only when the requirement is genuinely ambiguous.
6. Never implement future features without explicit instruction.

The goal is not to write the most code.

The goal is to build a stable, maintainable, testable, and production-ready system incrementally.
