# AI Career Recommendation System

# AI Architecture & Processing Documentation

## 1. Purpose

This document defines how Artificial Intelligence is used inside the AI Career Recommendation System.

The purpose is to ensure that AI usage is:

- Structured
- Cost-efficient
- Testable
- Explainable
- Reproducible
- Secure
- Separated from normal application logic

AI must not be used for tasks that can be reliably handled using deterministic application logic.

---

# 2. AI Responsibility

AI is primarily responsible for:

CV understanding
Skill extraction
Natural language interpretation
Career recommendation reasoning
Recommendation explanation
Skill improvement suggestions

```

AI is NOT the source of truth for:


Authentication
Authorization
Student identity
Skill master data
Career master data
Job master data
Database relationships
Matching score calculation
Security validation
```

---

# 3. AI Processing Architecture

General flow:

Student
|
v
Upload CV
|
v
PDF Text Extraction
|
v
Clean CV Text
|
v
AI Skill Extraction
|
v
Skill Normalization
|
v
Student Skill Profile
|
+----------------------+
| |
v v
Career Matching Job Matching
| |
v v
AI Recommendation Matching Result
| |
+----------+-----------+
|
v
Recommendation
|
v
Dashboard
|
v
PDF Report

```

---

# 4. AI Provider Abstraction

The application must not tightly couple business logic to a specific AI provider.

Preferred architecture:


CareerRecommendationService
            |
            v
        AIService
            |
            v
      AI Provider
```

The provider can be changed without rewriting the entire application.

Possible providers may include:

OpenAI
Anthropic
Google Gemini
Other compatible provider

````

The actual provider should be configurable through environment variables.

---

# 5. Environment Configuration

API credentials must never be hardcoded.

Example:

```env
AI_PROVIDER=
AI_API_KEY=
AI_MODEL=
AI_TIMEOUT=
AI_MAX_TOKENS=
````

The actual variables may be adjusted according to the selected SDK/provider.

Never commit:

API keys
Secret keys
Access tokens
Production credentials

```

to Git.

---

# 6. AI Service

Create a dedicated service responsible for communicating with the AI provider.

Example:


app/Services/AI/AIService.php
```

Possible responsibilities:

- Send AI requests.
- Handle provider configuration.
- Handle timeout.
- Handle API errors.
- Parse provider responses.
- Return normalized application-level results.

Business-specific services should call `AIService`.

Example:

SkillExtractionService
|
v
AIService
|
v
AI Provider

```

---

# 7. AI Tasks

The system initially contains four major AI tasks:


1. Skill Extraction
2. Career Recommendation
3. Career Recommendation Explanation
4. Skill Improvement Suggestion
```

Job matching should primarily use deterministic matching logic.

AI may optionally be used to explain matching results.

---

# 8. Task 1 — Skill Extraction

Purpose:

Extract relevant skills from the student's CV.

Input:

Cleaned CV Text

````

Output:

Structured JSON.

Example:

```json
{
  "skills": [
    {
      "name": "Laravel",
      "confidence": 0.95
    },
    {
      "name": "PHP",
      "confidence": 0.98
    },
    {
      "name": "MySQL",
      "confidence": 0.91
    }
  ]
}
````

The AI must NOT directly create database records.

The application must validate the response first.

---

# 9. Skill Extraction Flow

CV PDF
|
v
PDF Parser
|
v
Raw Text
|
v
Text Cleaner
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
JSON Response
|
v
Response Validation
|
v
Skill Normalization
|
v
Database

```

---

# 10. Skill Extraction Rules

AI should:

* Extract explicit technical skills.
* Extract relevant tools.
* Extract frameworks.
* Extract programming languages.
* Extract databases.
* Extract cloud technologies.
* Extract relevant soft skills when supported by evidence.
* Avoid guessing skills that are not supported by the CV.

AI should NOT:

* Invent skills.
* Assume expertise based solely on a job title.
* Treat every word as a skill.
* Infer advanced proficiency without evidence.
* Create new database skill records directly.

---

# 11. Skill Confidence

Confidence represents how strongly the CV supports the existence of a skill.

Example:


Laravel → 0.95
PHP → 0.98
Docker → 0.70
```

Confidence does not represent actual proficiency.

The system must distinguish:

Skill existence confidence

```

from:


Skill proficiency
```

---

# 12. Skill Normalization

AI may return:

Laravel Framework
Laravel
laravel framework
PHP Laravel

```

The application should normalize these values.

Preferred process:


AI Output
   |
   v
Normalize String
   |
   v
Search Existing Skill
   |
   +---- Found ----> Existing Skill
   |
   +---- Not Found -> Review / Controlled Creation
```

Skill normalization should preferably use:

- Lowercase comparison.
- Slug matching.
- Alias mapping.
- Controlled synonym mapping.

Example:

Laravel Framework
Laravel
laravel framework

```

may resolve to:


Laravel
```

---

# 13. Skill Alias Strategy

If necessary, create a controlled alias mechanism.

Example:

"ReactJS"
"React.js"
"React JS"

```

→


React
```

Aliases should be maintained by the application rather than relying entirely on AI.

---

# 14. Skill Proficiency

The initial system should avoid making strong proficiency claims unless the CV provides sufficient evidence.

If proficiency is estimated, use evidence such as:

Years of experience
Projects
Job responsibilities
Certifications
Repeated usage
Professional experience

```

Avoid statements such as:


Student knows Laravel → Expert
```

without supporting evidence.

---

# 15. Task 2 — Career Recommendation

Purpose:

Recommend suitable career paths based on:

Student Profile
Student Skills
Skill Confidence
Career Requirements
Skill Gap
Experience
Education

```

Preferred architecture:


Student Data
     |
     v
Skill Analysis
     |
     v
Career Candidate Selection
     |
     v
CareerRecommendationService
     |
     v
AIService
     |
     v
AI Provider
     |
     v
Structured Recommendation
```

---

# 16. Career Candidate Selection

AI should not be asked to choose from an unlimited universe of careers.

Preferred:

Database Career Master
|
v
Candidate Careers
|
v
Matching Engine
|
v
Top Candidates
|
v
AI Reasoning

```

This reduces:

* Hallucination.
* Uncontrolled career names.
* Invalid recommendations.
* AI token usage.

---

# 17. Career Matching Score

The core matching score should be deterministic.

Example:


Career Requirements:

PHP
Laravel
MySQL
Docker
Redis

Student:

PHP
Laravel
MySQL

Matched:
3 / 5

Score:
60%
```

AI may explain the score but should not arbitrarily change the score.

---

# 18. Career Recommendation Strategy

Preferred hybrid approach:

Structured Matching +
AI Reasoning
=
Final Recommendation

```

Structured matching determines:


Matching Score
Existing Skills
Missing Skills
Career Candidate
```

AI determines:

Why the career fits
Natural-language explanation
Improvement suggestions
Potential career direction

````

---

# 19. Career Recommendation Output

Expected structure:

```json
{
  "career_id": 1,
  "matching_score": 85,
  "reason": "The student has strong backend development skills...",
  "strengths": [
    "PHP",
    "Laravel",
    "MySQL"
  ],
  "skill_gaps": [
    "Docker"
  ],
  "suggestions": [
    "Learn containerization using Docker"
  ]
}
````

The application must validate:

career_id
matching_score
reason
strengths
skill_gaps
suggestions

```

before storing the result.

---

# 20. AI Must Not Invent Career IDs

AI should not generate arbitrary database IDs.

Preferred:


Application:
Career ID = 12
Career = Backend Developer

AI:
Provides reasoning for career ID 12
```

The application owns the actual career ID.

---

# 21. Task 3 — Career Explanation

Career explanation is a natural-language task.

Input:

Career
Student Skills
Skill Gap
Matching Score
Experience

```

Output:


Reason
Strengths
Skill Gaps
Improvement Suggestions
```

The AI should explain existing application data.

It should not introduce unsupported facts.

---

# 22. Task 4 — Skill Improvement Suggestions

AI may generate learning recommendations based on missing skills.

Example:

Missing Skill:
Docker

Suggestion:
Learn Docker fundamentals, container lifecycle,
Docker Compose, and basic deployment workflows.

```

Suggestions should remain realistic and relevant to the selected career.

---

# 23. Job / Internship Matching

Job matching should primarily be deterministic.

Flow:


Student Skills
      |
      v
Job Requirements
      |
      v
Matching Engine
      |
      v
Matching Score
```

Example:

Job Requirements:
Laravel
PHP
MySQL
Docker

Student:
Laravel
PHP
MySQL

Score:
75%

```

AI may optionally provide:


Why this job matches
Skill gap explanation
Preparation suggestions
```

---

# 24. Job Data Integrity

AI must never invent:

Company
Position
Salary
Location
Deadline
Application URL
Job URL

```

Job information must come from:


Database
Admin
Validated external source
```

AI can explain a job.

AI cannot become the authoritative job database.

---

# 25. AI Prompt Architecture

Prompts should be versioned.

Example:

Skill Extraction:
skill-extraction-v1

Career Recommendation:
career-recommendation-v1

Career Explanation:
career-explanation-v1

```

When a prompt changes significantly:


v1 → v2
```

Do not silently overwrite the old prompt version.

---

# 26. Prompt Design Principles

Prompts should:

- Define the role.
- Define the task.
- Define the available data.
- Define output format.
- Define constraints.
- Explicitly prohibit hallucination.
- Require structured output.
- Keep instructions concise.

Example concept:

You are a career analysis assistant.

Analyze the provided CV text.

Extract only skills that are supported
by evidence in the CV.

Return valid JSON.

Do not invent skills.
Do not include unsupported information.

````

---

# 27. Structured Output

Whenever possible, AI responses should use structured JSON.

Preferred:

```json
{
  "skills": []
}
````

Avoid relying on free-form text for data that must be stored in the database.

Bad:

The student knows PHP, Laravel, MySQL...

````

Better:

```json
{
  "skills": [
    "PHP",
    "Laravel",
    "MySQL"
  ]
}
````

---

# 28. AI Response Validation

AI output is external/untrusted data.

Every AI response must be validated.

Flow:

AI Response
|
v
JSON Parse
|
v
Schema Validation
|
v
Business Validation
|
v
Database

```

Validation must verify:

* Required fields.
* Data types.
* Allowed values.
* Numeric ranges.
* Existing database IDs.
* Maximum lengths.

---

# 29. AI Hallucination Prevention

The system should minimize hallucination through:


Controlled master data
+
Structured prompts
+
Structured output
+
Database validation
+
Deterministic matching
```

Do not rely only on prompt instructions.

---

# 30. AI Cost Optimization

AI usage should be intentionally minimized.

Important principle:

Do not call AI if deterministic logic can solve the problem.

```

Examples:

### Do not use AI for:


Counting skills
Calculating matching score
Checking if skill exists
Checking database relationships
Filtering active jobs
Calculating dashboard statistics
```

### AI may be used for:

CV understanding
Skill extraction
Natural language reasoning
Recommendation explanation
Learning suggestions

```

---

# 31. CV Reprocessing Strategy

The same CV should not be analyzed repeatedly without a reason.

Preferred:


Upload CV
   |
   v
Analyze
   |
   v
Store Result
```

When the student opens the dashboard:

Read Stored Result

```

Do NOT:


Open Dashboard
   |
   v
Call AI Again
```

---

# 32. Recommendation Caching

Recommendations should be stored.

Example:

career_recommendations
job_matches

```

When the student returns to the dashboard:


Database
   |
   v
Existing Recommendation
```

not:

Dashboard
|
v
AI Request

```

unless the user explicitly requests re-analysis.

---

# 33. Re-analysis

Provide an explicit action:


Analyze Again
```

when required.

Flow:

Student
|
v
Analyze Again
|
v
Create New Analysis Version
|
v
AI Processing
|
v
Store New Result

```

This makes AI usage intentional.

---

# 34. AI Request Logging

Important AI events should be logged.

Example:


AI skill extraction started
AI skill extraction completed
AI skill extraction failed
AI recommendation started
AI recommendation completed
AI response validation failed
```

Do not log:

API keys
Passwords
Authentication tokens
Sensitive personal information
Full CV contents unless absolutely necessary

```

---

# 35. AI Error Handling

Possible errors:


Timeout
Rate Limit
Invalid API Key
Provider Error
Invalid JSON
Invalid Schema
Empty Response
Token Limit
```

The application should handle these gracefully.

Example:

AI Processing Failed
|
v
Mark Analysis as Failed
|
v
Store Error Metadata
|
v
Show User-Friendly Message

```

Do not expose provider-specific technical errors directly to users.

---

# 36. Retry Strategy

Retries should be limited.

Recommended:


Temporary network/provider error
        |
        v
Retry
        |
        v
Failure
```

Do not retry:

Invalid request
Invalid API key
Invalid prompt
Invalid schema

```

indefinitely.

---

# 37. Queue Recommendation

AI operations may eventually be processed asynchronously.

Potential jobs:


ProcessCVJob
ExtractSkillsJob
GenerateCareerRecommendationJob
GenerateReportJob
```

Initial implementation may remain synchronous if the project scope is small.

Introduce queues when:

- Processing becomes slow.
- User experience requires asynchronous processing.
- AI requests become expensive.
- Multiple tasks need background execution.

---

# 38. AI Dataset

A controlled dataset should be created for testing.

Example:

Dataset
|
+---- CV 001
+---- CV 002
+---- CV 003
+---- CV 004
+---- CV 005

```

Each test CV should have expected information.

Example:


CV:
Backend developer with PHP, Laravel and MySQL experience.

Expected Skills:
PHP
Laravel
MySQL
```

---

# 39. AI Evaluation

Skill extraction should be evaluated using:

Precision
Recall
F1 Score

```

Conceptually:


Precision =
Correct Extracted Skills
/
Total Extracted Skills
```

Recall =
Correct Extracted Skills
/
Total Expected Skills

```


F1 =
2 × Precision × Recall
/
Precision + Recall
```

The exact evaluation implementation can be introduced during the testing phase.

---

# 40. Career Recommendation Evaluation

Career recommendations can be evaluated using:

Relevance
Skill Alignment
Reason Quality
Skill Gap Accuracy
Recommendation Consistency

```

The evaluation should compare AI recommendations against:


Student Skills
Career Requirements
Expected Career
```

---

# 41. AI Model Configuration

The model should be configurable.

Do not hardcode a model name in multiple services.

Preferred:

config/ai.php

````

or equivalent configuration.

Example:

```php
return [
    'provider' => env('AI_PROVIDER'),
    'model' => env('AI_MODEL'),
];
````

The exact implementation depends on the selected AI SDK.

---

# 42. AI Provider Switching

The application should allow changing providers with minimal changes.

Preferred:

AIService
|
+---- OpenAIProvider
|
+---- AnthropicProvider
|
+---- GeminiProvider

```

Only implement providers that are actually required.

Do not over-engineer the first version.

---

# 43. AI Security

Never send unnecessary data to external AI providers.

Only send data required for the task.

For example:

Skill extraction may require:


CV text
```

It does not necessarily require:

Password
Authentication token
Internal database IDs
Unrelated student data

```

---

# 44. Personal Data Consideration

CVs may contain personal information.

The system should:

* Minimize unnecessary AI exposure.
* Avoid logging CV content.
* Secure uploaded CV files.
* Restrict CV access.
* Avoid sending unrelated personal data.
* Delete data according to the application's retention policy when applicable.

---

# 45. AI Processing State

AI-related operations should have explicit states.

Example:


pending
processing
completed
failed
```

This allows the frontend to display:

Analyzing CV...
Analysis completed
Analysis failed

```

without guessing the processing state.

---

# 46. Recommended AI Service Structure

Possible structure:


app/
└── Services/
    └── AI/
        ├── AIService.php
        ├── SkillExtractionService.php
        ├── CareerRecommendationService.php
        ├── CareerExplanationService.php
        └── Providers/
            └── AIProvider.php
```

Actual structure may be simplified if the project does not require provider abstraction immediately.

---

# 47. AI Workflow Summary

## CV Analysis

Upload CV
↓
Extract Text
↓
Clean Text
↓
AI Skill Extraction
↓
Validate
↓
Normalize Skills
↓
Store Skills

```

## Career Recommendation


Student Skills
   ↓
Career Requirements
   ↓
Calculate Matching
   ↓
Select Candidates
   ↓
AI Explanation
   ↓
Store Recommendation
```

## Job Matching

Student Skills
↓
Job Requirements
↓
Calculate Matching
↓
Rank Jobs
↓
Optional AI Explanation

```

---

# 48. AI Golden Rules

Claude MUST follow these rules:


1. AI is not the database.
2. AI is not the source of truth.
3. AI output is untrusted.
4. Validate every AI response.
5. Prefer deterministic logic when possible.
6. Do not call AI unnecessarily.
7. Store reusable AI results.
8. Version important prompts.
9. Do not expose API credentials.
10. Do not allow AI to invent database entities.
11. Do not allow AI to invent job information.
12. Keep AI integration isolated.
13. Make AI processing testable.
14. Minimize sensitive data sent to AI.
15. Keep AI costs under control.
```

---

# 49. Development Priority

AI development should follow this order:

Phase 1
AI Provider Connection
↓
Phase 2
CV Text Extraction
↓
Phase 3
Skill Extraction
↓
Phase 4
Skill Normalization
↓
Phase 5
Deterministic Skill Matching
↓
Phase 6
Career Recommendation
↓
Phase 7
Job Matching
↓
Phase 8
AI Evaluation
↓
Phase 9
Optimization

```

Do not implement all AI features simultaneously.

---

# 50. Final Principle

The AI system should follow:


Structured Data
      +
Deterministic Logic
      +
AI Reasoning
      +
Validation
      =
Reliable Recommendation System
```

The goal is not to use AI everywhere.

The goal is to use AI only where it provides meaningful value.
