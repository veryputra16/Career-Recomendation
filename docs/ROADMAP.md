# AI Career Recommendation System

# Development Roadmap

## 1. Purpose

This document defines the development roadmap for the AI Career Recommendation System.

The project is developed incrementally using small, isolated tasks.

The purpose of this roadmap is to:

- Keep development organized.
- Reduce AI context usage.
- Reduce unnecessary AI credit consumption.
- Prevent unrelated code changes.
- Make every feature independently testable.
- Make development progress easy to track.

---

# 2. Development Rules

## Task Execution

Each task should be completed independently.

Claude MUST NOT automatically continue to the next task.

After completing a task:

1. Test the implementation.
2. Update the task status.
3. Report modified files.
4. Suggest a Git commit.
5. Stop.

---

## Task Status

Use the following status:

[ ] Not Started
[~] In Progress
[x] Completed
[-] Skipped / Not Required

```

---

# 3. Project Development Flow

The general system flow is:


Authentication
      ↓
Student Profile
      ↓
CV Upload
      ↓
PDF Text Extraction
      ↓
CV Analysis
      ↓
Skill Extraction
      ↓
Skill Normalization
      ↓
Skill Gap Analysis
      ↓
Career Recommendation
      ↓
Internship / Job Matching
      ↓
Dashboard Analytics
      ↓
PDF Career Report
      ↓
Testing & Deployment
```

---

# PHASE 0 — Project Planning & Documentation

## TASK-000 — Project Documentation Structure

Status: [x]

Create the initial documentation structure:

docs/
├── CLAUDE.md
├── ROADMAP.md
├── ARCHITECTURE.md
├── DATABASE.md
├── AI.md
└── API.md

````

Requirements:

- Documentation files must have clearly separated responsibilities.
- Avoid duplicating the same information across documents.

---

## TASK-001 — Define System Requirements

Status: [ ]

Document:

- System objectives
- Target users
- Main features
- Functional requirements
- Non-functional requirements
- System limitations

---

## TASK-002 — Define System Architecture

Status: [ ]

Create:

`docs/ARCHITECTURE.md`

Document:

- Laravel architecture
- React + Inertia architecture
- Authentication flow
- CV processing flow
- AI processing flow
- Recommendation flow
- PDF generation flow

---

## TASK-003 — Define Database Design

Status: [ ]

Create:

`docs/DATABASE.md`

Document:

- Tables
- Columns
- Primary keys
- Foreign keys
- Relationships
- Indexes
- Important constraints

---

## TASK-004 — Define AI Architecture

Status: [ ]

Create:

`docs/AI.md`

Document:

- AI provider
- AI services
- Prompt architecture
- AI input
- AI output
- JSON structure
- Error handling
- Retry strategy
- Token/cost considerations

---

# PHASE 1 — Laravel Project Foundation

## TASK-010 — Initialize Laravel Project

Status: [ ]

Requirements:

- Laravel 12
- PHP 8.2+
- MySQL
- Environment configuration

Verify:

```bash
php artisan --version
php -v
````

---

## TASK-011 — Configure Database

Status: [ ]

Requirements:

- Configure `.env`
- Create MySQL database
- Configure database connection
- Run initial migrations

Test:

```bash
php artisan migrate
```

---

## TASK-012 — Install Inertia + React

Status: [ ]

Requirements:

- Configure Inertia
- Configure React
- Configure Vite
- Verify frontend rendering

Test:

```bash
npm run dev
```

---

## TASK-013 — Configure Tailwind CSS

Status: [ ]

Requirements:

- Install/configure Tailwind
- Verify Tailwind classes
- Create basic application styling

---

## TASK-014 — Create Base Application Layout

Status: [ ]

Create:

- Application layout
- Navigation
- Sidebar where appropriate
- Main content area
- Responsive layout

Do not build complete dashboards yet.

---

# PHASE 2 — Authentication & Authorization

## TASK-020 — Implement Authentication

Status: [ ]

Implement:

- Login
- Logout
- Authentication middleware
- Session handling

---

## TASK-021 — Student Registration

Status: [ ]

Implement:

- Student registration
- Validation
- User creation
- Student profile initialization

---

## TASK-022 — User Roles

Status: [ ]

Roles:

admin
student

```

Implement:

- Role storage
- Role middleware / authorization
- Backend authorization

---

## TASK-023 — Admin Authorization

Status: [ ]

Ensure:

- Admin can access admin modules.
- Student cannot access admin modules.

Test unauthorized access.

---

## TASK-024 — Student Authorization

Status: [ ]

Ensure students can only access their own:

- Profile
- CV
- Skills
- Recommendations
- Reports

---

# PHASE 3 — Student Profile

## TASK-030 — Student Profile Database

Status: [ ]

Create student profile structure.

Possible fields:

- User ID
- Student ID
- Name
- Email
- University
- Faculty
- Major
- Semester
- Graduation year
- Bio

Finalize structure according to `DATABASE.md`.

---

## TASK-031 — Student Profile Page

Status: [ ]

Create:

- Profile page
- Profile information
- Edit profile

---

## TASK-032 — Student Profile Validation

Status: [ ]

Implement:

- Backend validation
- Frontend validation feedback
- Error handling

---

# PHASE 4 — CV Upload

## TASK-040 — CV Database Structure

Status: [ ]

Create CV-related migration/model.

Possible information:

- Student ID
- Original filename
- Stored filename
- File path
- File size
- MIME type
- Processing status
- Upload timestamp

---

## TASK-041 — CV Upload Backend

Status: [ ]

Implement:

- PDF validation
- File size validation
- Secure filename
- Storage
- Database record

---

## TASK-042 — CV Upload Interface

Status: [ ]

Create:

- Upload CV form
- File selection
- Upload progress/state
- Success message
- Error message

---

## TASK-043 — CV Preview & Management

Status: [ ]

Implement:

- Current CV display
- CV metadata
- Download/view
- Delete/replace CV

Respect authorization.

---

## TASK-044 — CV Upload Testing

Status: [ ]

Test:

- Valid PDF
- Invalid file type
- Oversized file
- Empty upload
- Unauthorized access
- Replace CV
- Delete CV

---

# PHASE 5 — PDF Text Extraction

## TASK-050 — Select PDF Parser

Status: [ ]

Evaluate and select a suitable PDF text extraction library.

Requirements:

- Laravel compatibility
- PHP compatibility
- Local processing
- Reasonable extraction quality

Document the selected library in `docs/ARCHITECTURE.md`.

---

## TASK-051 — Implement PDF Text Extraction Service

Status: [ ]

Create dedicated service:


CVParserService
```

Responsibilities:

- Read CV PDF
- Extract text
- Return extracted text
- Handle extraction errors

---

## TASK-052 — Store Extracted CV Text

Status: [ ]

Add appropriate database structure.

Store:

- Extracted text
- Processing status
- Error status if applicable
- Processing timestamp

---

## TASK-053 — CV Processing Status

Status: [ ]

Implement statuses such as:

uploaded
processing
processed
failed

```

---

## TASK-054 — PDF Extraction Testing

Status: [ ]

Test with:

- Simple CV
- CV with multiple pages
- CV containing tables
- CV containing special characters
- Invalid PDF
- Empty PDF

Document known limitations.

---

# PHASE 6 — Skill Extraction

## TASK-060 — Define Skill Database

Status: [ ]

Create:


skills
student_skills
```

or the final normalized structure defined in `DATABASE.md`.

---

## TASK-061 — Skill Seeder / Initial Dataset

Status: [ ]

Create initial skill dataset.

Examples:

PHP
Laravel
React
JavaScript
Python
Java
MySQL
PostgreSQL
Git
Docker
AWS
Communication
Leadership
Problem Solving

```

The final dataset should be expanded according to project requirements.

---

## TASK-062 — AI Service Foundation

Status: [ ]

Create generic AI service.

Responsibilities:

- API communication
- Authentication
- Request handling
- Response handling
- Error handling
- Logging

Do not implement career recommendation yet.

---

## TASK-063 — Skill Extraction Prompt

Status: [ ]

Create the skill extraction prompt.

Input:


Extracted CV Text
```

Expected output:

```json
{
  "skills": [
    {
      "name": "Laravel",
      "confidence": 0.95
    }
  ]
}
```

---

## TASK-064 — Skill Extraction Service

Status: [ ]

Create:

SkillExtractionService

```

Flow:


CV Text
   ↓
AI
   ↓
Structured JSON
   ↓
Validation
   ↓
Skill Normalization
   ↓
Database
```

---

## TASK-065 — Skill Normalization

Status: [ ]

Normalize AI output against the skill database.

Handle:

- Case differences
- Duplicate skills
- Similar names
- Unknown skills

---

## TASK-066 — Skill Extraction UI

Status: [ ]

Student should be able to see:

- Processing status
- Extracted skills
- Confidence score where appropriate

---

## TASK-067 — Skill Extraction Testing

Status: [ ]

Test using multiple CV samples.

Measure:

- Correct skills
- False positives
- False negatives
- Invalid AI responses
- API failures

---

# PHASE 7 — Skill Gap Analysis

## TASK-070 — Define Career Skill Requirements

Status: [ ]

Create career master data.

Each career should have required/recommended skills.

Example:

Backend Developer

Laravel
PHP
MySQL
REST API
Git
Docker

```

---

## TASK-071 — Career Skill Database

Status: [ ]

Create:

- Careers
- Career skills
- Skill requirements

Finalize according to `DATABASE.md`.

---

## TASK-072 — Skill Gap Calculation

Status: [ ]

Compare:


Student Skills

VS

Career Required Skills
```

Output:

Existing Skills
Missing Skills
Skill Coverage

```

---

## TASK-073 — Skill Gap UI

Status: [ ]

Display:

- Existing skills
- Missing skills
- Coverage percentage
- Recommended skills to learn

---

# PHASE 8 — AI Career Recommendation

## TASK-080 — Career Recommendation Database

Status: [ ]

Create structure for storing recommendation results.

Store:

- Student
- Career
- Matching score
- Reason
- Recommendation date

---

## TASK-081 — Career Recommendation Prompt

Status: [ ]

Create structured AI prompt.

Input:

- Student profile
- Student skills
- Skill gaps
- Career requirements

---

## TASK-082 — Career Recommendation Service

Status: [ ]

Create:


CareerRecommendationService
```

Expected output should contain:

- Career
- Matching score
- Reason
- Existing skills
- Missing skills
- Suggestions

---

## TASK-083 — Recommendation Validation

Status: [ ]

Validate:

- Score range
- Career existence
- Required fields
- AI response structure

---

## TASK-084 — Career Recommendation UI

Status: [ ]

Display:

- Recommended careers
- Matching score
- Explanation
- Skill gaps
- Improvement suggestions

---

## TASK-085 — Recommendation History

Status: [ ]

Store recommendation history.

Student should be able to view previous analyses.

---

## TASK-086 — Career Recommendation Testing

Status: [ ]

Test:

- Different student profiles
- Different skill sets
- Empty skill data
- AI failure
- Invalid AI output

---

# PHASE 9 — Internship & Job Matching

## TASK-090 — Job/Internship Database

Status: [ ]

Create job/internship dataset.

Possible fields:

- Company
- Position
- Description
- Location
- Employment type
- Required skills
- Optional skills
- Deadline
- Source URL
- Status

---

## TASK-091 — Internship Seeder

Status: [ ]

Create realistic testing dataset.

Recommended initial dataset:

50–100 opportunities

```

---

## TASK-092 — Basic Skill Matching

Status: [ ]

Implement deterministic matching using student skills and required skills.

Do not rely entirely on AI.

---

## TASK-093 — Matching Score

Status: [ ]

Calculate:


Matching Score
```

based on relevant skills.

Document the scoring formula.

---

## TASK-094 — AI Matching Explanation

Status: [ ]

Use AI only where useful to explain:

- Why the position matches
- Strengths
- Missing skills
- Suggestions

AI must not invent job information.

---

## TASK-095 — Internship Recommendation UI

Status: [ ]

Display:

- Position
- Organization
- Matching score
- Matching skills
- Missing skills
- Explanation

---

## TASK-096 — Job Matching Testing

Status: [ ]

Test different skill profiles against the dataset.

---

# PHASE 10 — Student Dashboard

## TASK-100 — Dashboard Structure

Status: [ ]

Create student dashboard layout.

---

## TASK-101 — Student Statistics

Status: [ ]

Display:

- CV status
- Number of skills
- Career readiness
- Number of recommendations

---

## TASK-102 — Skill Visualization

Status: [ ]

Create appropriate charts for student skills.

Possible charts:

- Bar chart
- Radar chart

---

## TASK-103 — Career Recommendation Summary

Status: [ ]

Display top career recommendations.

---

## TASK-104 — Skill Gap Summary

Status: [ ]

Display important missing skills.

---

## TASK-105 — Internship Recommendation Summary

Status: [ ]

Display top matching internships/jobs.

---

# PHASE 11 — Admin Dashboard & Analytics

## TASK-110 — Admin Dashboard Structure

Status: [ ]

Create admin layout and dashboard.

---

## TASK-111 — Student Statistics

Status: [ ]

Display:

- Total students
- Active students
- Students with CV
- Students analyzed

---

## TASK-112 — Skill Distribution

Status: [ ]

Display:

- Most common skills
- Skill distribution
- Skill frequency

---

## TASK-113 — Career Distribution

Status: [ ]

Display:

- Most recommended careers
- Career distribution
- Recommendation statistics

---

## TASK-114 — Internship Matching Analytics

Status: [ ]

Display:

- Most matched positions
- Average matching score
- Internship statistics

---

## TASK-115 — Analytics Optimization

Status: [ ]

Optimize expensive analytics queries.

Use:

- Aggregation
- Indexes
- Caching where appropriate

Do not optimize prematurely.

---

# PHASE 12 — PDF Career Report

## TASK-120 — PDF Report Design

Status: [ ]

Define report structure.

Report sections:

Student Profile
CV Analysis
Skills
Skill Gap
Career Recommendations
Internship Recommendations
Career Readiness

```

---

## TASK-121 — PDF Report Service

Status: [ ]

Create:


PDFReportService
```

---

## TASK-122 — Generate Student Report

Status: [ ]

Implement:

- Generate PDF
- Download PDF
- Authorization

---

## TASK-123 — PDF Styling

Status: [ ]

Create professional report design.

---

## TASK-124 — PDF Testing

Status: [ ]

Test:

- Long CV
- Many skills
- Many recommendations
- Missing data
- PDF rendering

---

# PHASE 13 — Career Readiness Score

## TASK-130 — Define Career Readiness Formula

Status: [ ]

Define a transparent scoring methodology.

The formula must be documented.

Avoid making the score entirely dependent on AI.

---

## TASK-131 — Career Readiness Calculation

Status: [ ]

Implement deterministic calculation.

---

## TASK-132 — Career Readiness UI

Status: [ ]

Display:

- Overall score
- Score breakdown
- Improvement areas

---

# PHASE 14 — Analysis History

## TASK-140 — Analysis History Database

Status: [ ]

Store historical CV analyses.

---

## TASK-141 — Analysis History UI

Status: [ ]

Allow students to view:

- Previous CV analysis
- Skills
- Recommendations
- Score
- Date

---

## TASK-142 — Compare Analysis

Status: [ ]

Optional feature.

Allow students to compare two analyses.

Possible comparison:

Old Skills
New Skills
Added Skills
Removed Skills
Career Score Change

```

---

# PHASE 15 — Admin Management

## TASK-150 — Student Management

Status: [ ]

Admin can:

- View students
- Search students
- Filter students
- View student details

---

## TASK-151 — Skill Management

Status: [ ]

Admin can:

- Add skill
- Edit skill
- Delete skill
- Search skill

---

## TASK-152 — Career Management

Status: [ ]

Admin can:

- Add career
- Edit career
- Delete career
- Define required skills

---

## TASK-153 — Internship Management

Status: [ ]

Admin can:

- Add internship
- Edit internship
- Delete internship
- Manage status

---

# PHASE 16 — Security & Hardening

## TASK-160 — Authorization Audit

Status: [ ]

Review all protected resources.

---

## TASK-161 — File Security Audit

Status: [ ]

Review:

- CV access
- File validation
- File storage
- Download authorization

---

## TASK-162 — API Security

Status: [ ]

Review:

- Rate limiting
- API credentials
- AI API requests
- Error exposure

---

## TASK-163 — Input Validation Audit

Status: [ ]

Review all forms and endpoints.

---

# PHASE 17 — Testing

## TASK-170 — Authentication Tests

Status: [ ]

Test:

- Login
- Logout
- Registration
- Role authorization

---

## TASK-171 — CV Tests

Status: [ ]

Test:

- Upload
- Validation
- Parsing
- Delete
- Access control

---

## TASK-172 — AI Service Tests

Status: [ ]

Test:

- Valid response
- Invalid response
- API failure
- Timeout
- Missing fields

Use mocked AI responses where appropriate.

Do not unnecessarily consume AI API credits during automated tests.

---

## TASK-173 — Recommendation Tests

Status: [ ]

Test:

- Career matching
- Skill gap
- Recommendation storage
- Recommendation history

---

## TASK-174 — Job Matching Tests

Status: [ ]

Test:

- Skill matching
- Score calculation
- Missing skills
- Dataset filtering

---

## TASK-175 — PDF Tests

Status: [ ]

Test:

- Report generation
- Authorization
- Missing data
- Large reports

---

## TASK-176 — End-to-End Testing

Status: [ ]

Test complete flow:


Register
  ↓
Login
  ↓
Complete Profile
  ↓
Upload CV
  ↓
Extract CV
  ↓
Extract Skills
  ↓
Analyze Career
  ↓
View Recommendations
  ↓
View Internship Matching
  ↓
Generate PDF
```

---

# PHASE 18 — Dataset & AI Evaluation

## TASK-180 — Prepare CV Dataset

Status: [ ]

Prepare testing CV dataset.

Recommended:

50–100 CVs

```

Use anonymized or synthetic data where necessary.

---

## TASK-181 — Define Evaluation Metrics

Status: [ ]

Define metrics for:

- Skill extraction accuracy
- Career recommendation relevance
- Job matching relevance

---

## TASK-182 — Skill Extraction Evaluation

Status: [ ]

Compare:


Expected Skills

VS

AI Extracted Skills
```

Calculate appropriate metrics.

---

## TASK-183 — Career Recommendation Evaluation

Status: [ ]

Evaluate recommendation relevance using a defined evaluation methodology.

---

## TASK-184 — Job Matching Evaluation

Status: [ ]

Evaluate matching score and relevance.

---

## TASK-185 — AI Cost Evaluation

Status: [ ]

Measure:

- Average tokens/request
- Average processing cost
- Number of AI requests
- Average response time

Optimize prompts where appropriate.

---

# PHASE 19 — Performance & Optimization

## TASK-190 — Database Query Audit

Status: [ ]

Check:

- N+1 queries
- Missing indexes
- Slow queries

---

## TASK-191 — AI Processing Optimization

Status: [ ]

Optimize:

- Prompt length
- CV text length
- Number of AI calls
- Model selection
- Response format

---

## TASK-192 — Queue Processing

Status: [ ]

Move appropriate long-running operations into queues.

Examples:

- CV processing
- AI analysis
- PDF generation

---

## TASK-193 — Caching

Status: [ ]

Cache appropriate:

- Analytics
- Master data
- Career skill requirements

Avoid caching user-specific data incorrectly.

---

# PHASE 20 — UI/UX Finalization

## TASK-200 — Responsive Audit

Status: [ ]

Test:

- Desktop
- Tablet
- Mobile

---

## TASK-201 — Loading States

Status: [ ]

Implement:

- Loading indicators
- Skeletons where appropriate
- Processing states

---

## TASK-202 — Empty States

Status: [ ]

Implement meaningful empty states for:

- No CV
- No skills
- No recommendations
- No internships
- No analysis history

---

## TASK-203 — Error States

Status: [ ]

Implement user-friendly error states.

---

## TASK-204 — UI Consistency Audit

Status: [ ]

Review:

- Typography
- Spacing
- Buttons
- Forms
- Tables
- Cards
- Colors
- Icons

---

# PHASE 21 — Final Refactoring

## TASK-210 — Backend Refactoring

Status: [ ]

Review:

- Controllers
- Services
- Models
- Requests
- Policies

Remove unnecessary duplication.

---

## TASK-211 — Frontend Refactoring

Status: [ ]

Review:

- React components
- Hooks
- Layouts
- Reusable UI

---

## TASK-212 — Code Cleanup

Status: [ ]

Remove:

- Debug code
- Unused imports
- Unused variables
- Temporary files
- Unused dependencies

---

## TASK-213 — Documentation Audit

Status: [ ]

Update:

- CLAUDE.md
- ROADMAP.md
- ARCHITECTURE.md
- DATABASE.md
- AI.md
- API.md

Ensure documentation matches the actual implementation.

---

# PHASE 22 — Deployment

## TASK-220 — Production Environment

Status: [ ]

Prepare:

- Production `.env`
- Database
- Storage
- Queue
- Cache

---

## TASK-221 — Production Security

Status: [ ]

Verify:

- APP_DEBUG=false
- Secure credentials
- File permissions
- HTTPS
- API security

---

## TASK-222 — Production Database

Status: [ ]

Run:

- Production migrations
- Required seeders

Do not use development/test data in production.

---

## TASK-223 — Production Build

Status: [ ]

Build frontend assets.

Verify:

```bash
npm run build
```

---

## TASK-224 — Production Testing

Status: [ ]

Perform final smoke testing.

---

# PHASE 23 — Final Project Validation

## TASK-230 — Complete System Testing

Status: [ ]

Validate all major workflows.

---

## TASK-231 — Requirement Validation

Status: [ ]

Verify every functional requirement has been implemented.

---

## TASK-232 — AI Evaluation Report

Status: [ ]

Document:

- Dataset
- Methodology
- Metrics
- Results
- Limitations

---

## TASK-233 — Final Documentation

Status: [ ]

Complete technical documentation.

---

## TASK-234 — Final Release

Status: [ ]

Final checklist:

- [ ] Authentication works
- [ ] Authorization works
- [ ] CV upload works
- [ ] PDF extraction works
- [ ] Skill extraction works
- [ ] Skill gap works
- [ ] Career recommendation works
- [ ] Internship matching works
- [ ] Dashboard works
- [ ] PDF report works
- [ ] Testing completed
- [ ] Security reviewed
- [ ] Production build completed
- [ ] Documentation updated

---

# 4. Recommended Development Order

The recommended order is:

PHASE 0
Documentation

↓

PHASE 1
Project Foundation

↓

PHASE 2
Authentication

↓

PHASE 3
Student Profile

↓

PHASE 4
CV Upload

↓

PHASE 5
PDF Extraction

↓

PHASE 6
Skill Extraction

↓

PHASE 7
Skill Gap

↓

PHASE 8
Career Recommendation

↓

PHASE 9
Internship / Job Matching

↓

PHASE 10
Student Dashboard

↓

PHASE 11
Admin Analytics

↓

PHASE 12
PDF Report

↓

PHASE 13
Career Readiness

↓

PHASE 14
Analysis History

↓

PHASE 15
Admin Management

↓

PHASE 16
Security

↓

PHASE 17
Testing

↓

PHASE 18
AI Evaluation

↓

PHASE 19
Optimization

↓

PHASE 20
UI/UX Finalization

↓

PHASE 21
Refactoring

↓

PHASE 22
Deployment

↓

PHASE 23
Final Validation

```

---

# 5. AI Development Cost Strategy

This project uses AI-assisted development.

To minimize AI credit consumption:

## Rule 1 — One Major Feature at a Time

Do not ask Claude to implement several unrelated features in one session.

Bad:


Build CV upload, AI extraction, recommendation,
dashboard, job matching and PDF.
```

Good:

Implement TASK-041 only.

```

---

## Rule 2 — Separate Planning and Implementation

When a task is complex:


Step 1:
Analyze the task.

Step 2:
Identify required files.

Step 3:
Implement.

Step 4:
Test.
```

Do not ask Claude to redesign the entire project before every small task.

---

## Rule 3 — Reuse Existing Code

Before creating new components/services:

- Check whether an existing implementation can be reused.
- Modify existing code when appropriate.
- Avoid duplicate functionality.

---

## Rule 4 — AI API Cost Control

During development:

- Mock AI responses for automated tests.
- Avoid unnecessary AI calls.
- Do not repeatedly analyze the same CV.
- Store analysis results when appropriate.
- Use small test CVs during development.
- Keep prompts concise.
- Avoid sending unnecessary CV text to the AI.

---

# 6. Task Execution Template

When starting a new Claude session, use:

Read docs/CLAUDE.md.

Read the relevant task in docs/ROADMAP.md.

Implement TASK-XXX only.

Do not work on future tasks.

Do not modify unrelated modules.

After implementation:

- test the feature
- list modified files
- provide testing instructions
- suggest a git commit message

Then stop.

```

---

# 7. Current Project Progress

Current phase:


PHASE 0 — Project Planning & Documentation
```

Current task:

TASK-001

```

Update this section whenever the active development task changes.

---

# 8. Important Principle

This roadmap is a guide, not permission to implement everything at once.

Claude must always prioritize:


Current Task
    ↓
Required Dependencies
    ↓
Implementation
    ↓
Testing
    ↓
Documentation Update
    ↓
STOP
```

Never:

Current Task
↓
Current Task
↓
Future Features
↓
Refactor Everything
↓
Change Architecture

```

The objective is to build the system **incrementally, safely, and efficiently**.
```
