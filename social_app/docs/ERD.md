# Entity Relationship Diagram

Render this in GitHub, VS Code (Markdown Preview Mermaid), or https://mermaid.live
and export a PNG/PDF for your documentation.

```mermaid
erDiagram
    USERS ||--o{ POSTS : writes
    USERS ||--o{ COMMENTS : writes
    USERS ||--o{ LIKES : gives
    POSTS ||--o{ COMMENTS : has
    POSTS ||--o{ LIKES : receives

    USERS {
        int id PK
        varchar username UK
        varchar password "password_hash()"
        varchar full_name
        varchar bio
        varchar profile_image
        timestamp created_at
    }
    POSTS {
        int id PK
        int user_id FK
        text content
        varchar image
        timestamp created_at
    }
    COMMENTS {
        int id PK
        int post_id FK
        int user_id FK
        varchar content
        timestamp created_at
    }
    LIKES {
        int id PK
        int post_id FK
        int user_id FK
    }
```

`likes` has a UNIQUE key on (post_id, user_id) so a user can like a post only once.
All foreign keys use ON DELETE CASCADE.
