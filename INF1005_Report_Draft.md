# INF1005 Project Report Draft  
**Project Title:** Aroma Haven - Full-Stack Coffee E-Commerce Platform  
**Module Code:** INF1005  
**Group Number:** [FILL]  
**Class/Section:** [FILL]  
**Submission Date:** [FILL]  

**Team Members**
- [FILL: Full Name] - [FILL: Matric Number]
- [FILL: Full Name] - [FILL: Matric Number]
- [FILL: Full Name] - [FILL: Matric Number]
- [FILL: Full Name] - [FILL: Matric Number]

---

## Table of Contents
1. Executive Summary  
2. Project Scope and Requirements  
3. System Design Overview  
4. High-Level Architecture  
5. Deployment and Infrastructure Architecture  
6. Low-Level Architecture and Implementation  
7. Database Design  
8. Security Architecture  
9. Testing and Quality Assurance  
10. Individual Contributions  
11. Credentials for Assessment  
12. Challenges, Lessons Learned, and Future Improvements  
13. Conclusion  
14. Generative AI Usage Declaration  
15. Appendices  

---

## 1. Executive Summary
Aroma Haven is a full-stack web application designed to provide a complete specialty coffee browsing and ordering experience for end users, while giving administrators practical tools to manage products, users, and customer enquiries. The system was implemented using a PHP-based architecture with MySQL persistence and deployed on a Google Cloud virtual machine so that the solution can be assessed in a production-like environment.

The project objective was to build a functioning, secure, and usable e-commerce workflow that supports account registration, authenticated login, product discovery, cart management, checkout, and post-purchase confirmation. In addition to core shopping functionality, the project includes an interactive coffee personality quiz that recommends products based on structured preference input, and an admin console for catalog operations, contact handling, and user governance.

From an infrastructure perspective, the deployed stack uses an e2-medium VM (2 vCPU, 4 GB memory), Nginx, php-fpm, composer-managed dependencies, and PHPMailer/PHPAuth-based functionality. HTTPS was configured using certbot with Let's Encrypt certificates, and the public route uses DuckDNS so assessors can access the hosted application reliably over a domain endpoint.  

Overall, the system meets the module requirement of implementing and publishing a working website while also including optional features that go beyond baseline requirements, especially in deployment hardening, account security flow, recommendation logic, and admin operations.

---

## 2. Project Scope and Requirements
### 2.1 Functional Scope
The implemented scope includes the following major user-facing and admin-facing capabilities:
- User registration and login with persistent sessions.
- Email-based one-time password (OTP) verification after password login.
- Product catalog listing with filtering, searching, and pagination.
- Product details page with bean metadata and cart integration.
- Cart drawer with local storage persistence and quantity management.
- Authenticated checkout flow with server-side order creation.
- PayPal order creation and capture flow with amount validation.
- Confirmation page with server-backed receipt details.
- Contact form submission and persistence.
- Admin dashboard with product, contact, and user management.
- Admin role assignment and account suspension controls.
- Coffee personality quiz that maps preferences to recommended products.

### 2.2 Non-Functional Scope
The project also targeted practical non-functional goals:
- Security baseline through CSRF checks, session controls, and HTTPS.
- Stable deployment that remains assessable after submission.
- Structured code organization for maintainability.
- Server-authoritative order state transitions to reduce client tampering risk.
- Responsive interface behavior for common desktop/mobile usage.

### 2.3 Optional Features Beyond Minimum Requirements
The project includes multiple optional enhancements:
- Production cloud deployment (Google Cloud VM) instead of local-only hosting.
- TLS certificate automation with certbot and Let's Encrypt.
- Domain routing and accessibility via DuckDNS.
- Email OTP flow to add second-step login verification.
- Personality quiz recommendation engine with weighted scoring.
- Admin user governance (promote/demote admin, suspend/reactivate users).
- Contact-reply email workflow for admin customer support operations.

### 2.4 Out-of-Scope Items
The following items were intentionally not completed in this phase:
- Native mobile applications.
- Multi-currency and region-specific tax rules.
- Full card gateway implementation beyond current PayPal flow.
- Advanced recommendation models driven by machine learning telemetry.
- Complete CI/CD pipeline automation for one-click deployment.

---

## 3. System Design Overview
The solution follows a pragmatic layered PHP web architecture with clear separation of concerns between routing, business logic, data access, and presentation templates.

At a high level:
- **Presentation layer:** PHP-rendered pages under `public/` and shared UI under `includes/`.
- **Application/service layer:** reusable backend logic under `backend/` (auth, order lifecycle, admin helpers, contact processing, OTP).
- **Persistence layer:** MySQL tables accessed through PDO prepared statements.
- **Static assets layer:** CSS design system, JavaScript interactions, and media assets.

The design approach prioritizes:
- Ease of implementation within module timeframe.
- Readable and auditable PHP code paths.
- Secure defaults for sensitive workflows (authentication and checkout).
- Extensibility for optional admin and recommendation features.

The route structure is explicit and page-oriented. Public endpoints and API-style handlers are separated, for example:
- Page endpoints: `index.php`, `shop-coffee.php`, `coffee-product.php`, `checkout.php`, `profile.php`.
- Action endpoints: `route.php` (auth actions), `checkout_api.php`, `paypal_api.php`, `admin-route.php`.

This structure supports both server-side rendered views and targeted AJAX/API interactions where state integrity matters (notably checkout and payment steps).

---

## 4. High-Level Architecture
### 4.1 Actor Model
The system supports several logical actor classes:
- Guest users: browse products, take quiz, submit contact form.
- Customers: authenticated users who can manage profile and complete checkout.
- Admin users: privileged users managing catalog, contacts, and user governance.
- System services: SMTP service for outgoing mail, PayPal API for payment authorization/capture.

### 4.2 End-to-End Request Flow
Typical flow for a secure transaction is:
1. User accesses the application domain via DuckDNS.
2. Nginx receives HTTPS requests and forwards PHP requests to php-fpm.
3. PHP app executes route/controller logic and loads dependencies via composer autoload.
4. Business logic reads/writes MySQL through PDO prepared statements.
5. For payment operations, the app securely calls PayPal APIs server-side.
6. Result is rendered in server-side page templates or returned as JSON.

### 4.3 Core System Components
- **Frontend pages and templates**
  - Landing and catalog pages.
  - Product detail and recommendation pages.
  - Authentication and profile pages.
  - Admin pages for operations.
- **Backend domain modules**
  - Authentication and session management (PHPAuth).
  - OTP generation/verification and email dispatch.
  - Order service for pending/paid/failed transitions.
  - Admin helper utilities for role and suspension logic.
  - Contact processing and reply workflow.
- **Data layer**
  - User identity, user profile, role, and status tables.
  - Product catalog table.
  - Contact submission and optional reply logs.
  - Order, order item, and payment audit tables.

### 4.4 Architectural Rationale
This architecture was selected because it balances:
- Sufficient robustness for a deployable web system.
- Fast implementation velocity for team-based module timelines.
- Clear mapping between business features and source code ownership.
- Practical security control points around auth, state-changing actions, and payment integrity checks.

---

## 5. Deployment and Infrastructure Architecture
### 5.1 Hosting Topology
The website is deployed on a Google Cloud VM using:
- Machine type: `e2-medium` (2 vCPU, 4 GB RAM).
- Runtime stack: `Nginx` + `php-fpm` + PHP application code.
- Dependency handling: `composer`.
- Authentication package: `phpauth`.
- Mail features: `PHPMailer` configured through SMTP environment variables.

### 5.2 DNS, Routing, and TLS
- External accessibility is handled through DuckDNS.
- HTTPS is configured using certbot and Let's Encrypt certificates.
- Site URL and cookie domain are configured to the public domain endpoint, with secure cookies enabled in auth configuration.

### 5.3 Runtime Services and Security Posture
The deployment is designed for stable assessment access and secure transport:
- TLS termination at Nginx.
- Secure cookie flags enabled for authenticated sessions.
- Domain-bound cookie settings for consistency.
- Server-side `.env` based configuration for credentials and external integration keys.

### 5.4 Why This Deployment Is an Optional-Feature Strength
For a module project, many teams stop at localhost demonstration. This project deploys and hardens a real internet-facing stack with HTTPS, routing, runtime process separation, and payment integration, which directly demonstrates practical web systems engineering beyond minimum assignment expectations.

### 5.5 Deployment Assumptions to Verify Before Final Submission
- VM OS version: [FILL]
- Nginx site config path and key directives: [FILL]
- php-fpm pool/version details: [FILL]
- Firewall open ports and restrictions: [FILL]
- Process supervision/restart policy (systemd, etc.): [FILL]

---

## 6. Low-Level Architecture and Implementation
### 6.1 Code Organization
Project structure follows module-level separation:
- `public/`: user-facing pages and action endpoints.
- `backend/`: business and infrastructure logic.
- `includes/`: reusable view components and catalog helpers.
- `public/css/` and `public/js/`: frontend system styles and interaction logic.
- `backend/sql/`: schema scripts for order flow and optional reply logging.

This organization supports clean ownership and easier debugging, especially for team contribution tracking.

### 6.2 Authentication and Access Control
Authentication combines PHPAuth session management with custom project guards:
- `auth_guard.php` enforces login for protected pages.
- `guest_guard.php` prevents logged-in users from accessing guest-only routes.
- `admin_guard.php` enforces admin role checks and suspension checks.

Registration flow:
- Creates credentials in `phpauth_users`.
- Inserts profile details in `user_profiles`.
- Assigns default `customer` role.
- Creates `user_status` baseline record.

Login flow:
- Validates credentials via PHPAuth.
- Applies anti-abuse login attempt limits.
- Generates and sends OTP for second-step verification.
- Verifies OTP and then issues an authenticated session cookie.

### 6.3 OTP and Mail Workflow
OTP implementation is stateful and time-bound:
- Existing unused OTP codes are invalidated on new generation.
- New 6-digit code is stored with 10-minute expiry.
- SMTP mail delivery is handled through PHPMailer.
- Verification marks OTP as used, preventing replay.

This introduces additional account protection while remaining understandable for assessment.

### 6.4 Product Discovery and Personalization
Catalog rendering pulls active products from database and maps records into display-friendly bean models.

Implemented discovery features:
- Search by name/origin/tags.
- Filter by roast and origin.
- Price range filter and pagination.
- Product detail pages with metadata and related products.

Personality quiz implementation:
- Multi-step flow with server-validated answer progression.
- Weighted scoring model combining brew method, flavor profile, roast preference, intensity, and budget.
- Deterministic tie-break logic to ensure reproducible recommendations.
- Final redirect to recommended product page.

### 6.5 Cart and Checkout Logic
Cart behavior is client-side for UX speed, using localStorage (`ah_cart`), but order authority remains server-side:
- Cart drawer supports quantity stepper and accessibility controls (keyboard trap, focus management, escape close).
- Checkout page sends cart lines to `checkout_api.php`.
- Server recalculates subtotal, shipping, tax, and total from database prices.
- Pending order is persisted before PayPal order creation.

This design prevents direct client-side amount manipulation from becoming final order truth.

### 6.6 PayPal Payment Integration
Payment operations are handled in `paypal_api.php`:
- Obtains OAuth access token from PayPal.
- Creates PayPal checkout order based on local pending order total/currency.
- Attaches provider order ID to local order.
- Captures payment and validates captured amount/currency against local order record.
- Marks local order as paid/failed accordingly.
- Stores payment logs in `order_payments`.
- Creates session-bound confirmation payload for final receipt page.

This is a strong optional feature because it includes server-side verification, not just frontend SDK embedding.

### 6.7 Contact and Admin Operations
Contact feature:
- Validates input and consent.
- Stores enquiry in `contact_submissions`.

Admin feature set:
- Dashboard KPIs (products, contacts, users, suspended users, admins).
- Product CRUD-like operations with validation and slug management.
- Contact status updates and internal notes.
- Admin email replies using PHPMailer SMTP settings.
- User role promotion/demotion and suspension/reactivation.
- Optional audit trails where relevant tables exist.

Together, these features provide operational depth beyond a simple storefront prototype.

---

## 7. Database Design
### 7.1 Core Data Domains
The schema can be grouped into five domains:
- Identity and account domain.
- Catalog domain.
- Contact/support domain.
- Order/payment domain.
- Governance/admin domain.

### 7.2 Major Tables Referenced in Current Implementation
- `phpauth_users`
- `phpauth_sessions`
- `user_profiles`
- `roles`
- `user_roles`
- `user_status`
- `otp_codes`
- `products`
- `contact_submissions`
- `contact_replies` (optional but implemented in code path)
- `orders`
- `order_items`
- `order_payments`
- `admin_audit_logs` (optional usage if present)

### 7.3 Relational Integrity Patterns
The schema uses foreign-key linked records for consistency:
- Orders reference authenticated users.
-(Order items reference both order and product).
- Contact replies reference submission and optional admin responder.
- User role assignments and status records reference user IDs.

The order tables also include:
- Status state machine fields (`pending`, `paid`, `failed`, `cancelled`).
- Provider-order uniqueness constraints.
- Indexed fields for user/order lookup efficiency.

### 7.4 Data Integrity Approach
Data correctness mechanisms include:
- Server-side validation before inserts and updates.
- Prepared statements to avoid injection risks.
- Controlled status transitions for checkout lifecycle.
- Use of transactions in pending order creation and item insertion.

### 7.5 Database Artifacts to Attach
Include in appendix:
- ERD diagram with key PK/FK relationships.
- Snapshot of `orders_core.sql`.
- Any migration or seed scripts used for initial role/status setup.

---

## 8. Security Architecture
### 8.1 Authentication Security
- Credentials are managed through PHPAuth.
- Login introduces OTP verification before final session establishment.
- Suspended users are blocked from authenticated access.
- Admin routes are protected by dedicated admin guard logic.

### 8.2 Session and Cookie Security
- Session is initialized centrally.
- CSRF token is generated and stored in session.
- Sensitive POST/JSON action routes verify CSRF token via body/header.
- Auth config enforces secure cookie usage on HTTPS domain.

### 8.3 Transport Security
- HTTPS is enabled through Let's Encrypt certificates managed by certbot.
- Domain-level secure cookie settings reduce accidental mixed-mode session leakage.

### 8.4 Input and Data Handling Security
- User input is sanitized at processing boundaries.
- Validation constraints applied for registration, contact forms, admin updates, and checkout payloads.
- SQL operations use prepared statements across feature modules.

### 8.5 Payment Integrity Controls
Checkout flow defends against client-side tampering by:
- Rebuilding order totals server-side from canonical product prices.
- Verifying captured payment amount and currency against local order.
- Rejecting mismatched captures and marking order failure when integrity checks fail.

### 8.6 Known Gaps / Improvement Opportunities
- Add centralized rate limiting beyond login attempt session counter.
- Introduce stricter password policy checks at registration layer.
- Formalize audit log table provisioning across environments.
- Add structured security test scripts and automated dependency checks.

---

## 9. Testing and Quality Assurance
### 9.1 Test Strategy
The project used scenario-driven manual validation across:
- Public browsing and product discovery.
- Authentication lifecycle.
- Cart and checkout behavior.
- Admin workflows.
- Error conditions and invalid inputs.

Testing emphasis was placed on real user paths and state transitions, especially around authentication and payment events.

### 9.2 Functional Test Coverage Summary
Representative tested flows:
- Register user -> login -> OTP verify -> browse -> add cart -> checkout -> confirmation.
- Contact form submission with valid and invalid inputs.
- Admin login -> product update -> contact update/reply -> user role/suspension operations.
- Personality quiz full journey from first step to recommendation result.

### 9.3 Security-Oriented Validation
Security checks included:
- Missing/invalid CSRF token rejection on protected action endpoints.
- Suspension enforcement for blocked accounts.
- Admin access rejection for non-admin users.
- Checkout API requiring authenticated user context and valid JSON payload.
- PayPal capture mismatch path resulting in order failure rather than false success.

### 9.4 Data Integrity Validation
Validation focused on:
- Pending order creation with normalized cart lines.
- Correct creation of order item rows for each line.
- Payment log persistence after capture attempts.
- Receipt rendering only when verified confirmation state exists.

### 9.5 UI and UX Validation
Interface tests included:
- Responsive layout behavior across landing, shop, product, and checkout pages.
- Cart drawer interaction, quantity updates, and keyboard behavior.
- Filter/search updates and pagination state handling.
- Form repopulation and error feedback in contact and auth flows.

### 9.6 Suggested Test Evidence for Appendix
Add screenshots or logs for:
- OTP email and successful verification screen.
- CSRF rejection response example.
- PayPal sandbox successful capture path.
- Admin product/contact/user operations.
- Contact reply success/failure feedback state.
- Quiz recommendation output redirect behavior.

### 9.7 Test Outcome Notes
Final run status for submission should be filled clearly:
- Blocking defects open at submission time: [FILL]
- Known non-blocking defects: [FILL]
- Final retest status: [FILL]

---

## 10. Individual Contributions
Use point-form exactly as required by the project guide.

### Member 1: [FILL: Name + Matric]
- [FILL: Feature/module ownership]
- [FILL: Feature/module ownership]
- [FILL: Testing/deployment/design contribution]

### Member 2: [FILL: Name + Matric]
- [FILL: Feature/module ownership]
- [FILL: Feature/module ownership]
- [FILL: Testing/deployment/design contribution]

### Member 3: [FILL: Name + Matric]
- [FILL: Feature/module ownership]
- [FILL: Feature/module ownership]
- [FILL: Testing/deployment/design contribution]

### Member 4: [FILL: Name + Matric]
- [FILL: Feature/module ownership]
- [FILL: Feature/module ownership]
- [FILL: Testing/deployment/design contribution]

### Team Collaboration Summary
- Branching and integration approach: [FILL]
- Conflict resolution process: [FILL]
- Joint testing and review process: [FILL]

---

## 11. Credentials for Assessment
**Deployment URL:** [FILL]  

**Customer Account**
- Username: [FILL]
- Password: [FILL]

**Admin Account**
- Username: [FILL]
- Password: [FILL]

**Employee/Staff Account (if applicable)**
- Username: [FILL]
- Password: [FILL]

**Assessment Access Statement**  
All credentials above are prepared for testing and assessment and work with username + password only, without requiring 2FA or external authentication channels.

---

## 12. Challenges, Lessons Learned, and Future Improvements
### 12.1 Key Challenges Encountered
- Integrating payment workflows while preserving server-authoritative totals.
- Ensuring secure login flow with additional OTP step without breaking usability.
- Managing role-based admin controls with clear privilege boundaries.
- Building optional admin email reply functionality with environment-safe configuration.
- Aligning frontend interactivity (filters/cart drawer) with clean maintainable architecture.

### 12.2 How Challenges Were Addressed
- Checkout logic was split into local pending order creation and external provider capture validation.
- OTP flow used expiration and single-use enforcement with a dedicated verification page.
- Admin guard logic and helper functions centralized role and suspension checks.
- Mail settings abstracted through environment-driven utility lookup.
- CSS and component structure followed token-first design guidelines to reduce style drift.

### 12.3 Lessons Learned
- Server-side trust boundaries are essential for any payment-adjacent feature.
- Operational tooling (TLS, domain routing, secure cookies) substantially improves assessment readiness.
- Optional features are most valuable when they strengthen reliability and maintainability, not just UI novelty.
- Structured helper modules reduce duplicated logic and improve team parallel development.

### 12.4 Future Improvements
- Add automated regression tests for critical API endpoints.
- Introduce webhook handling for asynchronous payment reconciliation.
- Expand recommendation logic to include order history and personalization memory.
- Add stronger observability (request logging, alerting, and health checks).
- Implement full CI/CD pipeline for repeatable deployment and rollback.

---

## 13. Conclusion
This project delivers a complete and deployable web system that satisfies core INF1005 expectations while meaningfully extending functionality through optional engineering features. Aroma Haven includes robust account handling, recommendation-driven product discovery, server-validated checkout, and a practical admin operations console.

The deployed architecture demonstrates real-world web systems concerns: secure transport, role-protected operations, environment-based configuration, and payment integrity handling. These decisions improve both user trust and assessor confidence during evaluation.

In summary, the team produced not only a functioning website, but also a technically coherent full-stack solution with clear extensibility for future iterations.

---

## 14. Generative AI Usage Declaration
**gAI tools used in this project report drafting support:** [FILL: Yes/No and tool names]  
**Scope of usage:** [FILL: e.g., outline generation, language polishing, formatting support]  
**Estimated proportion of gAI-generated final report content:** [FILL: must be <= 10%]  

We acknowledge the use of generative AI tools in accordance with module and SIT Library policy. All final submitted content was reviewed, verified, and curated by the project team, and technical accuracy remains the team's responsibility.

---

## 15. Appendices
### Appendix A - Architecture Diagram
- [FILL: high-level deployment and request flow diagram]

### Appendix B - Database Design Evidence
- ERD image: [FILL]
- Key schema scripts:
  - `backend/sql/orders_core.sql`
  - `backend/sql/optional_contact_replies.sql`

### Appendix C - Test Evidence
- Authentication + OTP screenshots: [FILL]
- Checkout + payment confirmation screenshots: [FILL]
- Admin operations screenshots: [FILL]
- Contact workflow screenshots: [FILL]

### Appendix D - Deployment Evidence
- VM instance details screenshot: [FILL]
- HTTPS certificate status screenshot: [FILL]
- Domain resolution / accessibility screenshot: [FILL]

### Appendix E - Optional Features Summary (Implemented)
- Cloud deployment on Google Cloud VM (e2-medium).
- Domain accessibility using DuckDNS.
- HTTPS with certbot + Let's Encrypt.
- OTP login verification via PHPMailer SMTP.
- PayPal server-validated checkout and capture flow.
- Admin governance for product/contact/user operations.
- Personality quiz recommendation engine.

