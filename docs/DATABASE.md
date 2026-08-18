# AI Career Recommendation System

# Database Documentation

## 1. Purpose

This document defines the database architecture for the AI Career Recommendation System.

The database uses a normalized relational design.

The database is responsible for storing:

- User accounts
- Student profiles
- CV files
- CV processing results
- Skills
- Student skills
- Career master data
- Career skill requirements
- Career recommendations
- Internship/job opportunities
- Job skill requirements
- Job matching results
- Analysis history
- Reports

This document is the primary reference for database-related development.

Claude MUST read this document before:

- Creating migrations
- Modifying migrations
- Creating database models
- Creating relationships
- Creating seeders
- Writing complex database queries

---

# 2. Database Technology

Database:

MySQL 8+

```

ORM:


Laravel Eloquent ORM
```

Character Set:

utf8mb4

```

Collation:


utf8mb4_unicode_ci
```

The actual production database configuration may use another compatible `utf8mb4` collation if required by the hosting environment.

---

# 3. Database Design Principles

The database follows:

- Relational database principles
- Normalization
- Referential integrity
- Foreign key constraints
- Appropriate indexing
- Clear ownership of data

Avoid:

- Duplicate data
- Unnecessary JSON columns
- Polymorphic relationships unless necessary
- Storing calculated values unnecessarily
- Storing AI responses without a defined purpose

JSON may be used when the data is genuinely semi-structured, especially for AI metadata or raw responses, but important searchable business data should remain relational.

---

# 4. High-Level Entity Relationship

The main relationship is:

users
|
+---- students
|
+---- cv_uploads
|
+---- student_skills
|
+---- career_recommendations
|
+---- job_matches
|
+---- analysis_histories
|
+---- reports

```

Master data:


skills
  |
  +---- student_skills
  |
  +---- career_skills
  |
  +---- job_skills
```

Career:

careers
|
+---- career_skills
|
+---- career_recommendations

```

Job / Internship:


jobs
  |
  +---- job_skills
  |
  +---- job_matches
```

---

# 5. Core Tables

Initial database tables:

users
students

cv_uploads
cv_analysis_histories

skills
student_skills

careers
career_skills

career_recommendations

jobs
job_skills
job_matches

reports

```

Additional tables may be introduced if required.

---

# 6. Users

Table:


users
```

Purpose:

Stores authentication information for all system users.

Primary key:

id

```

Recommended fields:


id
name
email
password
role
email_verified_at
remember_token
created_at
updated_at
```

Role values:

admin
student

```

Notes:

- Authentication data belongs to `users`.
- Student-specific information belongs to `students`.
- Do not place all student profile information inside `users`.

---

# 7. Students

Table:


students
```

Purpose:

Stores student-specific profile information.

Recommended fields:

id
user_id
student_number
full_name
university
faculty
major
semester
graduation_year
bio
created_at
updated_at

```

Relationship:


users
  1
  |
  |
  1
students
```

Foreign key:

students.user_id
references
users.id

```

Recommended constraint:


unique(user_id)
```

A user with role `student` should have one student profile.

---

# 8. CV Uploads

Table:

cv_uploads

```

Purpose:

Stores uploaded CV metadata and processing status.

Recommended fields:


id
student_id
original_filename
stored_filename
file_path
file_size
mime_type
status
extracted_text
uploaded_at
processed_at
created_at
updated_at
```

Possible status values:

uploaded
processing
processed
failed

```

Relationship:


students
  1
  |
  N
cv_uploads
```

Foreign key:

cv_uploads.student_id
references
students.id

```

---

# 9. CV File Storage

The database must not store the actual PDF binary unless explicitly required.

The database stores metadata and the storage path.

Example:


file_path:
cv/01JXXXXXX.pdf
```

The physical file should be stored using Laravel Storage.

Recommended:

storage/app/private/cv

```

CV files should not automatically be publicly accessible.

Access should be authorized through the application.

---

# 10. CV Analysis History

Table:


cv_analysis_histories
```

Purpose:

Stores historical CV analysis results.

This allows the system to preserve previous AI analyses instead of overwriting them.

Recommended fields:

id
student_id
cv_upload_id
analysis_version
ai_provider
ai_model
prompt_version
status
summary
raw_result
analyzed_at
created_at
updated_at

```

Possible status:


processing
completed
failed
```

`raw_result` may use JSON when required.

However, important business information should also be stored in normalized tables.

Relationship:

students
1
|
N
cv_analysis_histories

```

and:


cv_uploads
  1
  |
  N
cv_analysis_histories
```

---

# 11. Skills

Table:

skills

```

Purpose:

Master data containing normalized skills.

Recommended fields:


id
name
slug
category
description
is_active
created_at
updated_at
```

Examples:

PHP
Laravel
React
JavaScript
Python
Java
MySQL
PostgreSQL
Docker
Git
AWS
Communication
Leadership
Problem Solving

```

Skill categories may include:


technical
soft_skill
tool
language
framework
database
cloud
other
```

The exact category list may evolve.

---

# 12. Student Skills

Table:

student_skills

```

Purpose:

Many-to-many relationship between students and skills.

Recommended fields:


id
student_id
skill_id
confidence_score
proficiency_level
source
created_at
updated_at
```

Possible source values:

cv_ai
manual
admin

```

Relationship:


students
   N
   |
   |
   N
student_skills
   |
   |
   N
skills
```

Recommended unique constraint:

unique(student_id, skill_id)

```

A student should not have duplicate skill records.

---

# 13. Confidence Score

`confidence_score` represents the confidence of the extraction process.

Example:


0.95
```

Recommended range:

0.00 - 1.00

```

Do not confuse:


confidence_score
```

with:

proficiency_level

```

Confidence means:

> How confident the extraction system is that the skill exists.

Proficiency means:

> How capable the student is at the skill.

The application should not assume AI confidence equals actual proficiency.

---

# 14. Careers

Table:


careers
```

Purpose:

Master data for possible career paths.

Recommended fields:

id
name
slug
description
category
is_active
created_at
updated_at

```

Examples:


Backend Developer
Frontend Developer
Fullstack Developer
Mobile Developer
Data Analyst
Data Scientist
UI/UX Designer
DevOps Engineer
Cloud Engineer
Cybersecurity Analyst
```

Career data should be controlled by the application/admin.

AI should not freely invent career records.

---

# 15. Career Skills

Table:

career_skills

```

Purpose:

Defines skills required or recommended for each career.

Recommended fields:


id
career_id
skill_id
importance
minimum_level
created_at
updated_at
```

Possible importance values:

required
recommended
optional

```

Relationship:


careers
   N
   |
   |
   N
career_skills
   |
   |
   N
skills
```

Recommended unique constraint:

unique(career_id, skill_id)

```

---

# 16. Career Recommendations

Table:


career_recommendations
```

Purpose:

Stores career recommendations generated for students.

Recommended fields:

id
student_id
career_id
cv_analysis_history_id
matching_score
reason
existing_skills
missing_skills
suggestions
recommendation_source
created_at
updated_at

```

Some fields such as:


existing_skills
missing_skills
suggestions
```

may be JSON if required for preserving the recommendation snapshot.

However, the normalized `skills` tables remain the source of truth for actual skill relationships.

Possible recommendation source:

ai
rule_based
hybrid

```

Preferred:


hybrid
```

where deterministic application data is combined with AI reasoning.

---

# 17. Career Recommendation Score

`matching_score` represents the calculated match between a student and a career.

Recommended range:

0 - 100

```

The score should have a documented calculation methodology.

Do not allow the AI model to arbitrarily determine the final score without validation.

The final scoring methodology should be documented in:


docs/AI.md
```

---

# 18. Jobs and Internships

Table:

jobs

```

Purpose:

Stores job and internship opportunities.

Recommended fields:


id
company_name
position
description
location
employment_type
type
source
source_url
application_url
deadline
status
created_at
updated_at
```

Possible `type`:

internship
job

```

Possible `status`:


active
inactive
expired
```

The system must not invent job opportunities.

Job information must originate from:

- Admin-created data
- Validated dataset
- Trusted external source/API

---

# 19. Job Skills

Table:

job_skills

```

Purpose:

Defines skills required or preferred for each job/internship.

Recommended fields:


id
job_id
skill_id
importance
created_at
updated_at
```

Possible importance:

required
preferred
optional

```

Relationship:


jobs
  N
  |
  |
  N
job_skills
  |
  |
  N
skills
```

Recommended unique constraint:

unique(job_id, skill_id)

```

---

# 20. Job Matches

Table:


job_matches
```

Purpose:

Stores the matching result between a student and a job/internship.

Recommended fields:

id
student_id
job_id
matching_score
matched_skills
missing_skills
explanation
matching_method
created_at
updated_at

```

Possible matching methods:


rule_based
ai
hybrid
```

Preferred:

hybrid

```

where the actual score is calculated from structured data and AI is used for explanation where appropriate.

---

# 21. Reports

Table:


reports
```

Purpose:

Stores generated PDF report metadata.

Recommended fields:

id
student_id
analysis_history_id
file_path
file_name
report_type
generated_at
created_at
updated_at

```

Possible report types:


career_analysis
career_report
```

The actual PDF file should be stored using Laravel Storage.

---

# 22. Relationships Summary

## User → Student

User
1
|
1
Student

```

---

## Student → CV


Student
1
|
N
CV Upload
```

---

## Student → Skills

Student
N
|
N
Skill

```

through:


student_skills
```

---

## Career → Skills

Career
N
|
N
Skill

```

through:


career_skills
```

---

## Job → Skills

Job
N
|
N
Skill

```

through:


job_skills
```

---

## Student → Career Recommendation

Student
1
|
N
Career Recommendation
N
|
1
Career

```

---

## Student → Job Match


Student
1
|
N
Job Match
N
|
1
Job
```

---

# 23. Foreign Key Rules

All important relationships should use foreign keys.

Recommended behavior:

## Student

students.user_id
ON DELETE CASCADE

```

If a user is permanently deleted, the student profile may be deleted.

---

## CV


cv_uploads.student_id
ON DELETE CASCADE
```

---

## Student Skills

student_skills.student_id
ON DELETE CASCADE

student_skills.skill_id
ON DELETE CASCADE

```

---

## Career Skills


career_skills.career_id
ON DELETE CASCADE

career_skills.skill_id
ON DELETE CASCADE
```

---

## Job Skills

job_skills.job_id
ON DELETE CASCADE

job_skills.skill_id
ON DELETE CASCADE

```

---

## Recommendations

For historical recommendation data, deletion behavior must be considered carefully.

Prefer preserving historical data where possible.

Do not automatically cascade-delete historical analysis records unless explicitly required.

---

# 24. Indexing Strategy

Indexes should be added to frequently queried fields.

Expected indexes:


users.email

students.user_id
students.student_number

cv_uploads.student_id
cv_uploads.status

skills.slug
skills.category

student_skills.student_id
student_skills.skill_id

careers.slug
careers.category

career_skills.career_id
career_skills.skill_id

career_recommendations.student_id
career_recommendations.career_id
career_recommendations.matching_score

jobs.status
jobs.type
jobs.deadline

job_skills.job_id
job_skills.skill_id

job_matches.student_id
job_matches.job_id
job_matches.matching_score
```

Do not create indexes blindly.

Review actual query patterns before adding excessive indexes.

---

# 25. Unique Constraints

Expected unique constraints:

users.email

students.user_id

skills.slug

careers.slug

student_skills(student_id, skill_id)

career_skills(career_id, skill_id)

job_skills(job_id, skill_id)

```

Additional unique constraints may be introduced when required.

---

# 26. Soft Deletes

Soft deletes may be used for master data where historical records must be preserved.

Candidates:


skills
careers
jobs
```

However, do not automatically add `SoftDeletes` to every table.

For transactional/history tables such as:

career_recommendations
job_matches
cv_analysis_histories

```

preserve historical records unless there is a clear requirement for deletion.

---

# 27. Timestamps

Laravel timestamps should generally be used:


created_at
updated_at
```

Additional timestamps may be used for business events.

Examples:

uploaded_at
processed_at
analyzed_at
generated_at

```

Do not use `created_at` as a replacement for a meaningful business timestamp when the distinction matters.

---

# 28. AI Data Storage

AI responses should not automatically be stored in their entirety.

When raw AI responses are needed for:

- Debugging
- Evaluation
- Reproducibility
- Research

they may be stored in JSON form.

Potential fields:


raw_result
ai_provider
ai_model
prompt_version
```

Do not store unnecessary sensitive information.

---

# 29. Analysis Versioning

AI analysis should support versioning.

Recommended metadata:

analysis_version
ai_provider
ai_model
prompt_version
analyzed_at

```

Example:


analysis_version:
1

ai_provider:
openai

ai_model:
configured-model

prompt_version:
skill-extraction-v1
```

This makes AI evaluation and historical comparison possible.

---

# 30. Data Lifecycle

Typical CV lifecycle:

Uploaded
↓
Processing
↓
Text Extracted
↓
Skills Extracted
↓
Analysis Completed
↓
Recommendations Generated

```

Possible failure:


Processing
   ↓
Failed
```

The system should preserve enough information to diagnose the failure.

---

# 31. Data Ownership

## Users

Authentication ownership.

## Students

Student profile ownership.

## CV Uploads

CV file and processing ownership.

## Skills

Normalized skill master data.

## Careers

Career master data.

## Jobs

Job/internship master data.

## Recommendations

Student-specific AI/rule-based recommendations.

## Reports

Generated report metadata.

---

# 32. Database vs AI Responsibility

The database is the source of truth for structured application data.

Examples:

Skills
Careers
Career Requirements
Jobs
Job Requirements
Students

```

AI is responsible for:


Extraction
Interpretation
Explanation
Recommendation assistance
```

AI must not become the source of truth for:

User accounts
Career master data
Job vacancies
Skill IDs
Authorization

```

---

# 33. Deterministic vs AI Processing

Prefer deterministic logic for:


Skill existence
Skill relationships
Career requirements
Job requirements
Matching score calculation
Authorization
Database validation
```

Use AI for:

CV skill extraction
Natural language interpretation
Career recommendation reasoning
Recommendation explanation
Skill improvement suggestions

```

Use a hybrid approach when appropriate.

---

# 34. Seeders

Seeders should provide initial master data.

Recommended seeders:


DatabaseSeeder

SkillSeeder

CareerSeeder

CareerSkillSeeder

JobSeeder

JobSkillSeeder
```

Use realistic but clearly marked test data during development.

Do not place sensitive personal data in seeders.

---

# 35. Factories

Factories should be created for entities that require repeated testing.

Candidates:

UserFactory
StudentFactory
CVUploadFactory
SkillFactory
CareerFactory
JobFactory

```

Factories should make feature testing easier without manually creating large datasets.

---

# 36. Migration Rules

Every database change must be represented by a Laravel migration.

Never modify an already-applied production migration simply to change the schema.

Instead:


Create new migration
       ↓
Modify schema
```

Example:

```bash
php artisan make:migration add_status_to_cv_uploads_table
```

---

# 37. Model Rules

Models must reflect database relationships.

Example:

Student

- belongsTo User
- hasMany CVUpload
- belongsToMany Skill
- hasMany CareerRecommendation
- hasMany JobMatch

```

Models should not contain large business workflows.

Complex workflows belong in Services.

---

# 38. Recommended Eloquent Relationships

Expected relationships:


User
    hasOne Student

Student
    belongsTo User
    hasMany CVUpload
    belongsToMany Skill
    hasMany CareerRecommendation
    hasMany JobMatch
    hasMany CVAnalysisHistory
    hasMany Report

CVUpload
    belongsTo Student
    hasMany CVAnalysisHistory

Skill
    belongsToMany Student
    belongsToMany Career
    belongsToMany Job

Career
    belongsToMany Skill
    hasMany CareerRecommendation

CareerRecommendation
    belongsTo Student
    belongsTo Career
    belongsTo CVAnalysisHistory

Job
    belongsToMany Skill
    hasMany JobMatch

JobMatch
    belongsTo Student
    belongsTo Job

Report
    belongsTo Student
    belongsTo CVAnalysisHistory
```

These relationships may be adjusted when the final schema is implemented.

---

# 39. Recommended Table Overview

| Table                  | Type        | Main Responsibility        |
| ---------------------- | ----------- | -------------------------- |
| users                  | Core        | Authentication             |
| students               | Core        | Student profile            |
| cv_uploads             | Transaction | CV metadata and processing |
| cv_analysis_histories  | History     | CV analysis versions       |
| skills                 | Master      | Normalized skills          |
| student_skills         | Pivot       | Student-skill relationship |
| careers                | Master      | Career definitions         |
| career_skills          | Pivot       | Career-skill requirements  |
| career_recommendations | Transaction | Career recommendations     |
| jobs                   | Master      | Jobs/internships           |
| job_skills             | Pivot       | Job-skill requirements     |
| job_matches            | Transaction | Student-job matching       |
| reports                | Transaction | PDF report metadata        |

---

# 40. Initial ERD Concept

┌──────────────┐
│ users │
├──────────────┤
│ id │
│ name │
│ email │
│ password │
│ role │
└──────┬───────┘
│
│ 1:1
▼
┌──────────────┐
│ students │
├──────────────┤
│ id │
│ user*id │
│ student_no │
│ university │
│ faculty │
│ major │
│ semester │
└──────┬───────┘
│
├───────────────┐
│ │
│ 1:N │ N:N
▼ ▼
┌──────────────┐ ┌───────────────┐
│ cv_uploads │ │student_skills │
├──────────────┤ ├───────────────┤
│ id │ │ student_id │
│ student_id │ │ skill_id │
│ file_path │ │ confidence │
│ status │ └───────┬───────┘
│ extracted... │ │
└──────┬───────┘ │
│ │
│ 1:N │ N:1
▼ ▼
┌──────────────────┐ ┌──────────────┐
│cv_analysis* │ │ skills │
│histories │ ├──────────────┤
├──────────────────┤ │ id │
│ id │ │ name │
│ student_id │ │ slug │
│ cv_upload_id │ │ category │
│ ai_model │ └──────┬───────┘
│ prompt_version │ │
│ raw_result │ │
└──────────────────┘ │
│
┌────────────────┴────────────────┐
│ │
▼ ▼
┌──────────────┐ ┌──────────────┐
│ careers │ │ jobs │
├──────────────┤ ├──────────────┤
│ id │ │ id │
│ name │ │ company │
│ description │ │ position │
│ category │ │ type │
└──────┬───────┘ │ status │
│ └──────┬───────┘
│ N:N │ N:N
▼ ▼
┌───────────────┐ ┌──────────────┐
│career_skills │ │ job_skills │
├───────────────┤ ├──────────────┤
│ career_id │ │ job_id │
│ skill_id │ │ skill_id │
│ importance │ │ importance │
└───────────────┘ └──────────────┘

students
│
├───────────────► career_recommendations
│ │
│ ▼
│ careers
│
└───────────────► job_matches
│
▼
jobs

```

---

# 41. Important Database Rules

Claude MUST NOT:

- Create duplicate skill tables.
- Create duplicate career tables.
- Store student skills as comma-separated strings.
- Store career requirements as comma-separated strings.
- Store job requirements as comma-separated strings.
- Put database credentials in source code.
- Create unnecessary JSON columns.
- Create relationships without foreign keys.
- Delete historical AI analysis without explicit requirement.
- Change the schema without updating this document.

---

# 42. Schema Change Workflow

When a feature requires a database change:


Feature Requirement
       ↓
Check DATABASE.md
       ↓
Identify required schema change
       ↓
Update DATABASE.md if necessary
       ↓
Create Migration
       ↓
Update Model
       ↓
Update Relationships
       ↓
Create/Update Factory
       ↓
Create/Update Seeder
       ↓
Test
```

The documentation should remain synchronized with the actual schema.

---

# 43. Current Database Status

Current status:

[ ] Database design finalized
[ ] Migrations created
[ ] Models created
[ ] Relationships implemented
[ ] Seeders created
[ ] Factories created
[ ] Database tests completed

```

Update this section as development progresses.

---

# 44. Final Principle

The database should remain:


Normalized
     +
Consistent
     +
Traceable
     +
Secure
     +
Scalable
```

The database is the source of truth for structured application data.

AI is a processing and recommendation layer, not the database itself.
