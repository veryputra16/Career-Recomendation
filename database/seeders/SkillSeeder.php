<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Programming Languages
            ['name' => 'PHP', 'category' => 'language', 'description' => 'Server-side scripting language primarily used for web development.'],
            ['name' => 'JavaScript', 'category' => 'language', 'description' => 'High-level, interpreted programming language for client and server side.'],
            ['name' => 'TypeScript', 'category' => 'language', 'description' => 'Typed superset of JavaScript that compiles to plain JavaScript.'],
            ['name' => 'Python', 'category' => 'language', 'description' => 'Interpreted high-level programming language widely used in AI, data science, and web development.'],
            ['name' => 'Java', 'category' => 'language', 'description' => 'Object-oriented programming language designed for portability and enterprise applications.'],
            ['name' => 'Kotlin', 'category' => 'language', 'description' => 'Modern statically typed language running on JVM, official for Android.'],
            ['name' => 'Dart', 'category' => 'language', 'description' => 'Client-optimized language for fast apps on any platform, used by Flutter.'],
            ['name' => 'SQL', 'category' => 'language', 'description' => 'Domain-specific language used in programming and managing relational databases.'],
            ['name' => 'HTML/CSS', 'category' => 'language', 'description' => 'Standard markup and stylesheet languages for designing web documents.'],

            // Frameworks & Libraries
            ['name' => 'Laravel', 'category' => 'framework', 'description' => 'Web application framework with expressive, elegant syntax for PHP.'],
            ['name' => 'React', 'category' => 'framework', 'description' => 'A JavaScript library for building user interfaces.'],
            ['name' => 'Vue.js', 'category' => 'framework', 'description' => 'Progressive JavaScript framework for building user interfaces.'],
            ['name' => 'Next.js', 'category' => 'framework', 'description' => 'React framework providing hybrid static & server rendering.'],
            ['name' => 'Express.js', 'category' => 'framework', 'description' => 'Fast, unopinionated, minimalist web framework for Node.js.'],
            ['name' => 'Spring Boot', 'category' => 'framework', 'description' => 'Java framework used to create stand-alone, production-grade Spring applications.'],
            ['name' => 'FastAPI', 'category' => 'framework', 'description' => 'Modern, fast (high-performance) web framework for building APIs with Python.'],
            ['name' => 'Flutter', 'category' => 'framework', 'description' => 'Open source framework by Google for building multi-platform applications.'],
            ['name' => 'Tailwind CSS', 'category' => 'framework', 'description' => 'Utility-first CSS framework for rapid UI development.'],
            ['name' => 'Inertia.js', 'category' => 'framework', 'description' => 'Bridge library connecting server-side frameworks like Laravel with modern SPAs like React.'],

            // Databases
            ['name' => 'MySQL', 'category' => 'database', 'description' => 'Open-source relational database management system.'],
            ['name' => 'PostgreSQL', 'category' => 'database', 'description' => 'Advanced open-source relational and object-relational database system.'],
            ['name' => 'MongoDB', 'category' => 'database', 'description' => 'Source-available cross-platform document-oriented database program.'],
            ['name' => 'Redis', 'category' => 'database', 'description' => 'In-memory data structure store used as a database, cache, and message broker.'],

            // Tools & Cloud / DevOps
            ['name' => 'Git', 'category' => 'tool', 'description' => 'Distributed version control system for tracking changes in source code.'],
            ['name' => 'Docker', 'category' => 'tool', 'description' => 'Platform designed to help developers build, share, and run modern applications.'],
            ['name' => 'Kubernetes', 'category' => 'tool', 'description' => 'Open-source system for automating deployment, scaling, and management of containerized apps.'],
            ['name' => 'AWS', 'category' => 'cloud', 'description' => 'Amazon Web Services cloud computing platform.'],
            ['name' => 'Google Cloud', 'category' => 'cloud', 'description' => 'Suite of cloud computing services provided by Google.'],
            ['name' => 'CI/CD Pipelines', 'category' => 'tool', 'description' => 'Automated continuous integration and continuous deployment workflows (GitHub Actions/GitLab CI).'],
            ['name' => 'Linux / Bash', 'category' => 'technical', 'description' => 'Unix-like operating system fundamentals and shell scripting.'],
            ['name' => 'Figma', 'category' => 'tool', 'description' => 'Collaborative web-based interface design and prototyping tool.'],
            ['name' => 'Postman', 'category' => 'tool', 'description' => 'API platform for building and using APIs.'],

            // Data Science & Machine Learning
            ['name' => 'Pandas / NumPy', 'category' => 'technical', 'description' => 'Fundamental Python libraries for data manipulation and numerical computation.'],
            ['name' => 'Scikit-learn', 'category' => 'framework', 'description' => 'Machine learning library in Python providing simple and efficient tools for predictive data analysis.'],
            ['name' => 'TensorFlow / PyTorch', 'category' => 'framework', 'description' => 'Open source deep learning frameworks for machine learning algorithms.'],
            ['name' => 'Tableau / PowerBI', 'category' => 'tool', 'description' => 'Interactive data visualization software focusing on business intelligence.'],

            // Soft Skills
            ['name' => 'Problem Solving', 'category' => 'soft_skill', 'description' => 'Ability to identify complex problems and review related information to develop solutions.'],
            ['name' => 'Communication', 'category' => 'soft_skill', 'description' => 'Effectively conveying technical and non-technical information across diverse audiences.'],
            ['name' => 'Team Leadership', 'category' => 'soft_skill', 'description' => 'Guiding, inspiring, and coordinating collaborative efforts in engineering teams.'],
            ['name' => 'Critical Thinking', 'category' => 'soft_skill', 'description' => 'Objective analysis and evaluation of an issue in order to form a judgment.'],
            ['name' => 'Time Management', 'category' => 'soft_skill', 'description' => 'Planning and exercising conscious control of time spent on specific activities.'],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['slug' => Str::slug($skill['name'])],
                [
                    'name' => $skill['name'],
                    'category' => $skill['category'],
                    'description' => $skill['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
