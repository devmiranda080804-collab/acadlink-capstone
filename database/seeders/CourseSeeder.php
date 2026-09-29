<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Real CBMA curriculum, transcribed from "CBMA PROGRAM MAJORS.pdf".
     * Course codes are reused across programs on purpose (e.g. CAE1 is
     * taught in all 6 programs) — Course rows are per-program, not shared,
     * matching the existing one-course-belongs-to-one-program architecture.
     *
     * semester_offered is one of 'First Semester', 'Second Semester', 'Summer'.
     * The PDF only labels a genuine extra term as "Summer" for BSA, where it's
     * called "Middle Term 1/2/3" — those are mapped to 'Summer' here since the
     * rest of the system (AcademicTerm/ProgramAssignment) only knows that 3-way
     * semester split.
     */
    public function run(): void
    {
        $courses = [];

        // Helper to push a block of courses sharing the same program/year/semester.
        $add = function (array &$courses, string $program, int $year, string $semester, array $items) {
            foreach ($items as [$code, $title]) {
                $courses[] = [
                    'code' => $code,
                    'title' => $title,
                    'program' => $program,
                    'year_level' => $year,
                    'semester_offered' => $semester,
                ];
            }
        };

        // ===================== BSMA =====================
        $add($courses, 'BSMA', 1, 'First Semester', [
            ['CAE1', 'Financial Accounting and Reporting'],
        ]);
        $add($courses, 'BSMA', 1, 'Second Semester', [
            ['CAE2', 'Partnership and Corporation Accounting'],
            ['CAE3', 'Law on Obligations and Contracts'],
            ['CAE4', 'Income Taxation'],
        ]);
        $add($courses, 'BSMA', 2, 'First Semester', [
            ['CAE5', 'Managerial Economics'],
            ['CAE6', 'Conceptual Frameworks and Accounting Standards'],
            ['CAE7', 'Intermediate Accounting 1'],
            ['PCA1', 'Human Behavior in Organization'],
        ]);
        $add($courses, 'BSMA', 2, 'Second Semester', [
            ['CAE8', 'Cost Accounting and Control'],
            ['CAE9', 'Business Laws and Regulations'],
            ['CAE10', 'Transfer and Business Taxation'],
            ['CAE11', 'Statistical Analysis with Software Application'],
            ['CAE12', 'Intermediate Accounting 2'],
            ['BMC1', 'Operations Management (Total Quality Management)'],
        ]);
        $add($courses, 'BSMA', 3, 'First Semester', [
            ['CAE13', 'Strategic Cost Management'],
            ['CAE14', 'Governance, Business Ethics, Risk Management and Internal Control'],
            ['CAE15', 'Regulatory Framework and Legal Issues in Business 1'],
            ['CAE16', 'Economic Development'],
            ['CAE17', 'Management Science (Quantitative Techniques in Business)'],
            ['CAE18', 'Financial Management'],
            ['CAE19', 'Intermediate Accounting 3'],
            ['PEA1', 'Human Resource Management'],
        ]);
        $add($courses, 'BSMA', 3, 'Second Semester', [
            ['PCA2', 'Sustainability and Strategic'],
            ['PCA3', 'Project Management'],
            ['CAE20', 'Strategic Business Analysis'],
            ['CAE21', 'Financial Markets'],
            ['CAE22', 'IT Application Tools'],
            ['CAE23', 'Accounting Information System'],
            ['CAE24', 'International Business and Trade'],
            ['CAE25', 'Accounting Research Methods'],
        ]);
        $add($courses, 'BSMA', 4, 'First Semester', [
            ['CAE26', 'Management Accounting Research'],
            ['CAE27', 'Accounting Internship (400 hours)'],
        ]);
        $add($courses, 'BSMA', 4, 'Second Semester', [
            ['PCA4', 'Accounting for Business Combinations'],
            ['PCA5', 'Accounting for Government and Non-Profit Organizations'],
            ['PCA6', 'Valuation Methods'],
            ['PCA7', 'Performance Management System'],
            ['PCA8', 'Strategic Tax Management'],
            ['BMC2', 'Strategic Management'],
            ['PEA2', 'Updates in Managerial Accounting'],
            ['PEA3', 'Innovation and Strategy Formulation'],
        ]);

        // ===================== BSA =====================
        $add($courses, 'BSA', 1, 'First Semester', [
            ['CAE1', 'Financial Accounting and Reporting'],
        ]);
        $add($courses, 'BSA', 1, 'Second Semester', [
            ['CAE2', 'Partnership and Corporation Accounting'],
            ['CAE3', 'Law on Obligations and Contracts'],
            ['CAE4', 'Income Taxation'],
        ]);
        $add($courses, 'BSA', 1, 'Summer', [
            ['CAE5', 'Managerial Economics'],
            ['CAE6', 'Conceptual Frameworks and Accounting Standards'],
        ]);
        $add($courses, 'BSA', 2, 'First Semester', [
            ['PCA1', 'Human Behavior in Organization'],
            ['CAE7', 'Intermediate Accounting 1'],
            ['CAE8', 'Cost Accounting Control'],
            ['CAE9', 'Business Laws Regulations'],
            ['CAE10', 'Transfer and Business Taxation'],
        ]);
        $add($courses, 'BSA', 2, 'Second Semester', [
            ['CAE11', 'Statistical Analysis with Software Application'],
            ['CAE12', 'Intermediate Accounting 2'],
            ['CAE13', 'Strategic Cost Management'],
            ['CAE14', 'Governance, Business Ethics, Risk Management and Internal Control'],
            ['CAE15', 'Regulatory Framework and Legal Issues in Business 1'],
            ['CAE16', 'Economic Development'],
        ]);
        $add($courses, 'BSA', 2, 'Summer', [
            ['CAE17', 'Management Science (Quantitative Techniques in Business)'],
            ['CAE18', 'Financial Management'],
            ['CAE19', 'Intermediate Accounting 3'],
        ]);
        $add($courses, 'BSA', 3, 'First Semester', [
            ['PCA2', 'Auditing and Assurance Principles'],
            ['CAE20', 'Strategic Business Analysis'],
            ['CAE21', 'Financial Markets'],
            ['CAE22', 'IT Application Tools in Business'],
            ['CAE23', 'Accounting Information System'],
            ['CAE24', 'International Business and Trade'],
        ]);
        $add($courses, 'BSA', 3, 'Second Semester', [
            ['PEA2', 'Updates in Financial Reporting Standards'],
            ['PCA3', 'Accounting for Government and Non-Profit Organizations'],
            ['PCA4', 'Accounting for Special Transactions'],
            ['PCA5', 'Accounting for Business Combinations'],
            ['PCA6', 'Auditing and Assurance Concepts and Applications'],
            ['PCA7', 'Auditing in a Computer Information System (CIS) Environment'],
        ]);
        $add($courses, 'BSA', 3, 'Summer', [
            ['PCA8', 'Auditing and Assurance Specialized Industries'],
            ['PEA3', 'Principles and Methods of Teaching Accounting'],
            ['CAE25', 'Accounting Research Methods'],
        ]);
        $add($courses, 'BSA', 4, 'First Semester', [
            ['CAE26', 'Accountancy Research'],
            ['CAE27', 'Accounting Internship (400 hours)'],
        ]);
        $add($courses, 'BSA', 4, 'Second Semester', [
            ['CAE28', 'Strategic Tax Management'],
            ['CAE29', 'Regulatory Framework and Legal Issues in Business 2'],
            ['PEA4', 'Operations Auditing'],
        ]);

        // ===================== BSOA =====================
        $add($courses, 'BSOA', 1, 'First Semester', [
            ['CAE1', 'Financial Accounting and Reporting'],
        ]);
        $add($courses, 'BSOA', 1, 'Second Semester', [
            ['OAC1', 'Keyboarding and Documents Processing'],
        ]);
        $add($courses, 'BSOA', 2, 'First Semester', [
            ['OAC2', 'Foundation of Shorthand'],
            ['PEO1', 'Customer Analytics'],
        ]);
        $add($courses, 'BSOA', 2, 'Second Semester', [
            ['BMC1', 'Operations Management & Total Quality Management'],
            ['OAC3', 'Advanced Shorthand'],
            ['OAC4', 'Personal and Professional Development'],
            ['OAC5', 'Business Report Writing'],
        ]);
        $add($courses, 'BSOA', 3, 'First Semester', [
            ['OAC6', 'Integrated Software Applications (MIS Concept, Desktop Publishing, Word Processing, Spreadsheet, and Presentation)'],
            ['OAC7', 'Business Law (Obligations and Contracts)'],
            ['OAC8', 'Entrepreneurial Behavior and Competencies'],
            ['OAC9', 'Customer Relations'],
            ['OAC10', 'Events Management'],
            ['PEO2', 'Filipino Stenography'],
        ]);
        $add($courses, 'BSOA', 3, 'Second Semester', [
            ['OAC11', 'Taxation'],
            ['BMC2', 'Strategic Management'],
            ['OAC12', 'Administrative Office Procedures and Management'],
            ['PEO3', 'Web Design'],
            ['BPO1', 'Fundamentals of Business Process Outsourcing'],
        ]);
        $add($courses, 'BSOA', 4, 'First Semester', [
            ['OAC13', 'Internet Research for Business'],
            ['PEO4', 'Machine Shorthand'],
            ['PEO5', 'International Studies'],
            ['PEO6', 'Introduction to Project Management'],
            ['BPO2', 'Fundamentals of Business Process Outsourcing 2'],
        ]);
        $add($courses, 'BSOA', 4, 'Second Semester', [
            ['OAC14', 'On-the-Job Training (300 hours)'],
            ['OAC15', 'Office Internship - Legal/Medical Internship (300 hours)'],
        ]);

        // ===================== BSBA - Human Resource Management =====================
        $add($courses, 'BSBA-HRM', 1, 'First Semester', [
            ['CAE1', 'Financial Accounting and Reporting'],
        ]);
        $add($courses, 'BSBA-HRM', 1, 'Second Semester', [
            ['BPO1', 'Fundamentals of Business Process Outsourcing 101'],
            ['Entrep1', 'Fundamentals of Entrepreneurship'],
        ]);
        $add($courses, 'BSBA-HRM', 2, 'First Semester', [
            ['PCH1', 'Administrative Office Procedures and Management'],
            ['BAC1', 'Basic Microeconomics'],
            ['BPO2', 'Fundamentals of Business Process Outsourcing 102'],
        ]);
        $add($courses, 'BSBA-HRM', 2, 'Second Semester', [
            ['BAC2', 'Business Law (Obligations and Contracts)'],
            ['PCH2', 'Recruitment and Selection'],
            ['BMC1', 'Fundamentals of Financial Management'],
            ['Elective1', 'Entrepreneurial Management'],
        ]);
        $add($courses, 'BSBA-HRM', 3, 'First Semester', [
            ['Elective2', 'Logistics Management'],
            ['BAC3', 'Business Research'],
            ['BAC4', 'Human Resource Management'],
            ['BMC2', 'Operations Management and Total Quality Management'],
            ['PCH3', 'Labor Law and Legislation'],
            ['PCH4', 'Training and Development'],
        ]);
        $add($courses, 'BSBA-HRM', 3, 'Second Semester', [
            ['Elective3', 'Environmental Management System'],
            ['BAC5', 'Good Governance and Social Responsibility'],
            ['BAC6', 'Taxation (Income Taxation)'],
            ['PCH5', 'Compensation Administration'],
            ['PCH6', 'Organizational Development'],
            ['PCH7', 'Labor Relations and Negotiations'],
        ]);
        $add($courses, 'BSBA-HRM', 4, 'First Semester', [
            ['BMC3', 'Strategic Management'],
            ['Elective4', 'Project Management'],
            ['BAC7', 'International Business and Trade'],
            ['BAC8', 'Feasibility Study'],
            ['PCH8', 'Special Topics in Human Resource Management'],
        ]);
        $add($courses, 'BSBA-HRM', 4, 'Second Semester', [
            ['Practicum', 'Work Integrated Learning (600 hours)'],
        ]);

        // ===================== BSBA - Financial Management =====================
        $add($courses, 'BSBA-FM', 1, 'First Semester', [
            ['CAE1', 'Financial Accounting and Reporting'],
        ]);
        $add($courses, 'BSBA-FM', 1, 'Second Semester', [
            ['BPO1', 'Fundamentals of Business Process Outsourcing 101'],
            ['Entrep1', 'Fundamentals of Entrepreneurship'],
        ]);
        $add($courses, 'BSBA-FM', 2, 'First Semester', [
            ['BMC1', 'Financial Management'],
            ['PCF1', 'Banking and Financial'],
            ['BAC1', 'Basic Microeconomics'],
            ['BPO2', 'Fundamentals of Business Process Outsourcing 102'],
        ]);
        $add($courses, 'BSBA-FM', 2, 'Second Semester', [
            ['BAC2', 'Business Law (Obligations and Contracts)'],
            ['PCF2', 'Financial Risk Management'],
            ['BMC2', 'Operations Management and Total Quality Management'],
            ['Elective1', 'Entrepreneurial Management'],
        ]);
        $add($courses, 'BSBA-FM', 3, 'First Semester', [
            ['Elective2', 'Security Analysis'],
            ['BAC3', 'Business Research'],
            ['BAC4', 'Human Resource Management'],
            ['PCF3', 'Monetary Policy and Central Banking'],
            ['PCF4', 'Financial Analysis and Reporting'],
        ]);
        $add($courses, 'BSBA-FM', 3, 'Second Semester', [
            ['Elective3', 'Financial Controllership'],
            ['BAC5', 'Good Governance and Social Responsibility'],
            ['BAC6', 'Taxation (Income Taxation)'],
            ['PCF5', 'Credit and Collection'],
            ['PCF6', 'Capital Markets'],
            ['PCF7', 'Investment and Portfolio Management'],
        ]);
        $add($courses, 'BSBA-FM', 4, 'First Semester', [
            ['BMC3', 'Strategic Management'],
            ['Elective4', 'Global Finance with Electronic Banking'],
            ['BAC7', 'International Business and Trade'],
            ['BAC8', 'Feasibility Study'],
            ['PCF8', 'Special Topics in Financial Management'],
        ]);
        $add($courses, 'BSBA-FM', 4, 'Second Semester', [
            ['Practicum', 'Work Integrated Learning (600 hours)'],
        ]);

        // ===================== BSBA - Marketing Management =====================
        $add($courses, 'BSBA-MM', 1, 'First Semester', [
            ['CAE1', 'Financial Accounting and Reporting'],
        ]);
        $add($courses, 'BSBA-MM', 1, 'Second Semester', [
            ['Entrep1', 'Fundamentals of Entrepreneurship'],
        ]);
        $add($courses, 'BSBA-MM', 2, 'First Semester', [
            ['BMC1', 'Fundamentals of Financial Management'],
            ['PCM1', 'Marketing Management'],
            ['BAC1', 'Basic Microeconomics'],
        ]);
        $add($courses, 'BSBA-MM', 2, 'Second Semester', [
            ['BAC2', 'Business Law (Obligations and Contracts)'],
            ['BPO1', 'Fundamentals of Business Process Outsourcing 101'],
            ['PCM2', 'Advertising'],
            ['BMC2', 'Operations Management & Total Quality'],
            ['Elective1', 'Service Marketing'],
        ]);
        $add($courses, 'BSBA-MM', 3, 'First Semester', [
            ['Elective2', 'Industrial/Agricultural Marketing'],
            ['BAC3', 'Business Research'],
            ['BAC4', 'Human Resource Management'],
            ['PCM3', 'Professional Salesmanship'],
            ['PCM4', 'Product Management'],
            ['BPO2', 'Fundamentals of Business Process Outsourcing 102'],
        ]);
        $add($courses, 'BSBA-MM', 3, 'Second Semester', [
            ['Elective3', 'International Marketing'],
            ['BAC5', 'Good Governance and Social Responsibility'],
            ['BAC6', 'Taxation (Income Taxation)'],
            ['PCM5', 'Pricing Strategy'],
            ['PCM6', 'Marketing Research'],
            ['PCM7', 'Retail Management'],
        ]);
        $add($courses, 'BSBA-MM', 4, 'First Semester', [
            ['BMC3', 'Strategic Management'],
            ['Elective4', 'Strategic Marketing Management'],
            ['BAC7', 'International Business and Trade'],
            ['BAC8', 'Feasibility Study'],
        ]);
        $add($courses, 'BSBA-MM', 4, 'Second Semester', [
            ['Practicum', 'Work Integrated Learning (600 hours)'],
        ]);

        foreach ($courses as $c) {
            Course::updateOrCreate(
                ['code' => $c['code'], 'program' => $c['program']],
                $c
            );
        }
    }
}
