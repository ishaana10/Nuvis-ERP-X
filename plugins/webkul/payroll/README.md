# Nuvis ERP X - Payroll Management Plugin

The **Payroll Plugin** (`webkul/payroll`) provides comprehensive, enterprise-grade payroll management for AureusERP / Nuvis ERP X. Built on Laravel 11, PHP 8.3, and Filament v4, it seamlessly integrates salary structures, customizable salary rules, contract management, automated batch payslip computation, PDF exports, statutory contribution tracking, and double-entry accounting journal posting.

---

## 1. Directory Structure

```text
plugins/webkul/payroll/
├── composer.json                       # PSR-4 Autoloading (Webkul\Payroll\)
├── config/
│   └── filament-shield.php             # Filament Shield permission mapping
├── database/
│   ├── migrations/                     # Schema migrations for payroll entities
│   │   ├── 2026_01_01_000001_create_payroll_contribution_registers_table.php
│   │   ├── 2026_01_01_000002_create_payroll_structures_table.php
│   │   ├── 2026_01_01_000003_create_salary_rules_table.php
│   │   ├── 2026_01_01_000004_create_employee_contracts_table.php
│   │   ├── 2026_01_01_000005_create_payroll_periods_table.php
│   │   ├── 2026_01_01_000006_create_payroll_runs_table.php
│   │   ├── 2026_01_01_000007_create_payslips_table.php
│   │   └── 2026_01_01_000008_create_payslip_lines_table.php
│   ├── seeders/
│   │   └── PayrollSeeder.php          # Sample structures, default rules, demo contract
│   └── settings/
│       └── 2026_01_01_000000_create_payroll_settings.php
├── public/svg/payroll.svg              # Module SVG Icon
├── resources/
│   ├── views/
│   │   ├── pages/process-payroll.blade.php
│   │   └── pdf/payslip.blade.php       # DomPDF / HTML Payslip Template
├── src/
│   ├── Enums/                          # Domain Enums (PayslipState, ContractState, etc.)
│   ├── Filament/
│   │   └── Admin/
│   │       ├── Clusters/              # Reports Cluster
│   │       ├── Pages/                 # ProcessPayroll & PayrollSettingsPage
│   │       ├── Resources/             # EmployeeContractResource, PayslipResource, etc.
│   │       └── Widgets/               # PayrollOverviewWidget
│   ├── Models/                         # Eloquent Models with Company Isolation
│   ├── Policies/                       # Model Authorization Policies
│   ├── Services/                       # Core Payroll Business Services
│   │   ├── PayrollCalculator.php      # Math & Rule Evaluation Engine
│   │   ├── PayslipGenerator.php       # Batch Payslip Computation Service
│   │   ├── AccountingPoster.php       # Double-entry Journal Posting Engine
│   │   └── PayslipPdfExporter.php     # Payslip PDF Renderer
│   ├── Settings/                       # PayrollSettings Class
│   ├── PayrollPlugin.php               # Filament v4 Plugin Registration
│   └── PayrollServiceProvider.php     # Package Service Provider
└── tests/                              # Pest Test Suite (Unit & Feature Tests)
```

---

## 2. Core Entities & Data Model

| Entity | Purpose & Relationships |
| :--- | :--- |
| **`PayrollStructure`** | Salary structure template grouping multiple `SalaryRule`s (e.g., Standard Salaried, Executive). |
| **`SalaryRule`** | Earning, Allowance, Deduction, or Employer Contribution calculation rule. Supports fixed amounts, percentages, and custom math formulas. |
| **`EmployeeContract`** | Assigns an employee to a `PayrollStructure`, base wage, currency, pay schedule, and effective date range. |
| **`PayrollPeriod`** | Represents a pay cycle (Start Date, End Date, State). |
| **`PayrollRun`** | Batch payroll run executing payslip generation for active employee contracts in a pay period. |
| **`Payslip`** | Individual employee payslip recording calculated Gross Wage, Basic Wage, Deductions, Employer Contributions, and Net Wage. |
| **`PayslipLine`** | Itemized breakdown of each `SalaryRule` calculation result on a payslip. |
| **`ContributionRegister`** | Statutory tracking entity for government taxes, FNPF / Social Security, and health insurance funds. |

---

## 3. Core Business Logic Services

### `PayrollCalculator`
Evaluates salary rules in sequential priority order for an employee contract.
- **Fixed Amount:** Evaluates fixed numeric amounts.
- **Percentage:** Calculates percentage based on base contract wage or gross accumulator.
- **Formula Evaluation:** Safely parses and calculates math expressions (supporting variables `contract.wage`, `basic`, `gross`, `categories`).
- **Rule Categories:** Categorizes lines into `BASIC`, `ALW` (Allowance), `GROSS`, `DED` (Deduction), `COMP` (Employer Contribution), and `NET`.

### `PayslipGenerator`
- Finds all active employee contracts matching the selected company and pay period.
- Executes `PayrollCalculator` for each contract to generate a draft `Payslip` and corresponding `PayslipLine` records.

### `AccountingPoster`
Integrates with Nuvis ERP X's `Accounts` module upon payslip confirmation:
- Creates an `AccountMove` (Journal Entry) in posted status.
- **Debits:** Expense accounts (Salary Expense, Employer Contribution Expense).
- **Credits:** Payable and Statutory Liability accounts (Employee Net Pay Payable, Tax Payable, Social Security Payable).
- Prevents duplicate posting by validating `PayslipState`.

### `PayslipPdfExporter`
Renders formatted HTML / DomPDF payslips complete with company logo, employee details, earnings breakdown, statutory deductions, net pay summary, and signature blocks.

---

## 4. Filament Admin Navigation & Features

| Menu Item / Page | Description | Direct Route |
| :--- | :--- | :--- |
| **Nuvis Payroll Launcher** | Top-left 3x3 App Switcher Overlay | Topbar Icon |
| **Process Payroll** | Interactive quick payroll execution wizard | `/admin/process-payroll` |
| **Payslips** | Full payslip list, sheet recomputation, confirmation, and PDF download | `/admin/payslips` |
| **Payroll Runs** | Batch run manager with automated payslip generation and accounting posting | `/admin/payroll-runs` |
| **Employee Contracts** | Employee salary contracts and wage assignments | `/admin/employee-contracts` |
| **Payroll Structures** | Salary structure templates | `/admin/payroll-structures` |
| **Salary Rules** | Rule builder for earnings, deductions, and contributions | `/admin/salary-rules` |
| **Contribution Registers** | Statutory & government fund registers | `/admin/contribution-registers` |
| **Pay Periods** | Pay schedule period manager | `/admin/payroll-periods` |
| **Reporting Cluster** | Payroll Summary Analysis report breakdown | `/admin/payroll/reports/payroll-summary-reports` |
| **Payroll Settings** | Journal & default liability account mappings | `/admin/payroll-settings-pages` |

---

## 5. Installation & Terminal Setup Commands

To deploy or update the Payroll plugin on your server:

```bash
# 1. Fetch latest changes
git fetch origin payroll-plugin-7751911136209434615
git reset --hard origin/payroll-plugin-7751911136209434615

# 2. Dump composer autoloader
composer dump-autoload

# 3. Execute plugin installation (runs migrations, settings, & seeders)
php artisan payroll:install

# 4. Generate Shield permissions
php artisan shield:generate --all --option=permissions --panel=admin

# 5. Clear all framework, view, and route caches
php artisan view:clear
php artisan optimize:clear
```

---

## 6. Testing

The plugin includes a comprehensive Pest test suite covering unit calculation logic, batch payslip generation, and accounting journal posting:

```bash
vendor/bin/pest plugins/webkul/payroll/tests
```
