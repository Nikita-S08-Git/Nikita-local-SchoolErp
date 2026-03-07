# Contributing to School ERP

First off, thank you for considering contributing to School ERP! It's people like you that make School ERP such a great tool.

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

## Getting Started

1. Fork the repository
2. Clone your fork
3. Create a new branch for your feature
4. Make your changes
5. Test thoroughly
6. Submit a pull request

## Development Setup

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/School-Erp.git
cd School-Erp

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database and run migrations
php artisan migrate --seed
```

## Branch Naming Convention

- `feature/` - New features (e.g., `feature/add-library-module`)
- `fix/` - Bug fixes (e.g., `fix/attendance-report-bug`)
- `docs/` - Documentation changes (e.g., `docs/update-readme`)
- `refactor/` - Code refactoring (e.g., `refactor/user-authentication`)

## Making Changes

1. **Create a branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**
   - Follow the existing code style
   - Add comments where necessary
   - Update documentation if needed

3. **Test your changes**
   ```bash
   php artisan test
   ```

4. **Commit your changes**
   ```bash
   git add .
   git commit -m "Add your descriptive commit message"
   ```

5. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Open a Pull Request**
   - Go to the original repository
   - Click "Pull Request"
   - Fill in the template
   - Wait for review

## Commit Message Guidelines

We follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<scope>): <description>

[optional body]
```

### Types
- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

### Examples
```
feat(attendance): add export to Excel functionality
fix(auth): resolve login issue with special characters
docs(readme): update installation instructions
refactor(users): optimize user query performance
```

## Pull Request Process

1. Update the README.md with details of changes if needed
2. Update the CHANGELOG.md with your changes
3. Make sure your code passes all tests
4. Ensure your code follows the existing style
5. Add appropriate tests for your changes
6. Wait for review from maintainers

## Reporting Bugs

### Before Submitting a Bug Report

- Check the documentation to see if it's a configuration issue
- Search existing issues to avoid duplicates
- Collect information about the bug (error messages, steps to reproduce, etc.)

### How to Submit a Bug Report

Create an issue with the following information:
- Clear and descriptive title
- Steps to reproduce the behavior
- Expected behavior
- Actual behavior
- Error messages
- Screenshots (if applicable)
- Your environment (PHP version, Laravel version, OS, etc.)

## Suggesting Features

We love to hear your ideas! Create an issue with:
- Clear description of the feature
- Use case - who would benefit and how
- Possible implementation details (optional)
- Examples of similar features in other systems

## Code Style

- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add comments for complex logic
- Keep functions small and focused
- Use type hints where possible

## Testing

- Write tests for new features
- Ensure existing tests pass
- Aim for good code coverage
- Test with different user roles

## Documentation

- Update README.md for new features
- Add inline comments for complex code
- Update API documentation if applicable
- Include usage examples

## Questions?

Feel free to open an issue with the "question" label if you have any questions about contributing!

## Thank You!

Your contributions to open source, large or small, make projects like this possible. Thank you for taking the time to contribute.
