# Taste
- Communicates in Vietnamese and expects answers, explanations, and written deliverables (docs, reviews) in Vietnamese. Confidence: 0.9
- Prefers being shown several alternative solutions/approaches to compare rather than a single recommendation, then iterates toward a final choice. Confidence: 0.7
- When an issue is reported, wants it explained concretely, including which file/location it is in. Confidence: 0.65
- Uses a custom `/code-review` skill for reviews; unless told otherwise, output goes to `review.md` and an existing `review.md` should be updated rather than replaced/duplicated. Confidence: 0.85
- Review output should list only problems to fix or caveats — no praise, no listing of things already done well or already fixed — and should cover only the changed code, not pre-existing code. Confidence: 0.8
- Expects reviews to verify: WordPress coding standards via `phpcs.xml` or `composer run phpcs`, PHP 7.4–8.5 compatibility, WordPress 6.6–7.1 compatibility, type hints and return types everywhere possible (PHP 7.4-safe), removal of dead code, common security issues (SQL injection, XSS, input validation), and simplification/reuse of existing PHP/JS/WordPress functions. Confidence: 0.8
- After substantial changes, expects a full re-review that also verifies whether previously reported issues are actually resolved and updates the same review file. Confidence: 0.7
- Values consistent naming across related APIs and flags names that are inconsistent or easily confused between similar operations. Confidence: 0.6
- In admin UI, prefers minimal, low-visual-weight indicators (e.g., small gray pin/icon) over text chips or badges that consume column space or look clickable, and prioritizes keeping content columns wide. Confidence: 0.65
