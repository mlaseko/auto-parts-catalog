# Auto Parts Catalog Overview

This repository appears to implement an auto parts catalog application. This document is a starter overview and should be expanded with repository-specific details such as data model tables, relationships, VIN decoding flow, filtering behavior, and key business rules.

## What the catalog does

- Stores and presents automotive parts data
- Lets users narrow parts by vehicle fitment and other attributes
- May use VIN decoding to identify the exact vehicle configuration
- Supports filtering and navigation across categories, brands, and compatibility data

## Suggested architecture sections to document

### 1. Main concepts
- Vehicles
- Makes / brands
- Models
- Years
- Engines / trims / body styles
- Parts categories
- Parts / SKUs
- Fitments / applications
- Manufacturers / suppliers

### 2. Likely database tables
Document the actual tables in the project schema here, for example:

- `vehicles`
- `makes`
- `models`
- `years`
- `engines`
- `parts`
- `part_categories`
- `fitments`
- `manufacturers`
- `vin_decodes`

For each table, describe:
- Purpose
- Primary key
- Important columns
- Foreign keys
- How the application reads/writes it

### 3. Relationships
Examples:
- A make has many models
- A model has many year variants
- A part belongs to a category
- A fitment links a part to one or more vehicle definitions
- VIN decoding resolves a VIN into vehicle attributes used for part matching

### 4. VIN decoding
Describe the repository’s actual VIN logic here:
- Where VIN input is accepted
- Validation rules
- Which service/class performs decoding
- Whether decoding is local or uses an external API
- What decoded fields are stored or used for matching
- How the decoded result narrows catalog results

### 5. Filtering
Describe supported filters such as:
- Year
- Make
- Model
- Engine
- Trim
- Category
- Brand
- Availability
- Price

Also document:
- Filter order
- Query strategy
- How filters combine
- Any fallback behavior when VIN decoding is incomplete

### 6. User flows worth documenting
- Browse by category
- Search by part number
- Search by vehicle
- Search by VIN
- View compatible parts
- Add to cart / inquiry flow if applicable

### 7. Important technical details
- Key controllers/routes
- Key services/classes
- Templates/views used for catalog pages
- JavaScript-enhanced filtering behavior
- Caching/indexing/search optimizations

### 8. Data integrity and constraints
- Unique constraints
- Referential integrity rules
- Soft deletes / status flags
- Import/sync jobs for catalog updates

## Recommended next step
Replace this starter document with details extracted from the repository codebase and database schema so it accurately reflects the application.
