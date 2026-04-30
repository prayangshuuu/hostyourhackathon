<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->auditUsers();
        $this->auditHackathons();
        $this->auditSegments();
        $this->auditTeams();
        $this->auditTeamMembers();
        $this->auditSubmissions();
        $this->auditSubmissionFiles();
        $this->auditAnnouncements();
        $this->auditScoringCriteria();
        $this->auditScores();
        $this->auditSponsors();
        $this->auditFaqs();
        $this->auditHackathonOrganizers();
        $this->auditSettings();
        $this->auditNotifications();
        $this->addMissingIndexes();
    }

    public function down(): void
    {
        // Intentionally minimal: this migration aligns legacy databases; reversing safely is non-trivial.
    }

    protected function hasIndex(string $table, string $indexName): bool
    {
        return collect(Schema::getIndexes($table))->contains(fn (array $idx) => ($idx['name'] ?? '') === $indexName);
    }

    protected function auditUsers(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_banned')) {
                $table->boolean('is_banned')->default(false)->after('remember_token');
            }
            if (! Schema::hasColumn('users', 'banned_at')) {
                $table->timestamp('banned_at')->nullable()->after('is_banned');
            }
            if (! Schema::hasColumn('users', 'banned_reason')) {
                $table->string('banned_reason', 500)->nullable()->after('banned_at');
            }
            if (! Schema::hasColumn('users', 'ban_type')) {
                $table->enum('ban_type', ['manual', 'team_ban'])->nullable()->after('banned_reason');
            }
        });

        if (! $this->hasIndex('users', 'users_is_banned_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('is_banned');
            });
        }
    }

    protected function auditHackathons(): void
    {
        if (Schema::hasColumn('hackathons', 're_open_submission')) {
            Schema::table('hackathons', function (Blueprint $table) {
                $table->dropColumn('re_open_submission');
            });
        }

        Schema::table('hackathons', function (Blueprint $table) {
            if (! Schema::hasColumn('hackathons', 'leaderboard_public')) {
                $table->boolean('leaderboard_public')->default(false);
            }
            if (! Schema::hasColumn('hackathons', 'rules')) {
                $table->longText('rules')->nullable();
            }
            if (! Schema::hasColumn('hackathons', 'prizes')) {
                $table->longText('prizes')->nullable();
            }
        });
        if (! $this->hasIndex('hackathons', 'hackathons_status_index')) {
            Schema::table('hackathons', function (Blueprint $table) {
                $table->index('status');
            });
        }
        if (! $this->hasIndex('hackathons', 'hackathons_created_by_index')) {
            Schema::table('hackathons', function (Blueprint $table) {
                $table->index('created_by');
            });
        }
    }

    protected function auditSegments(): void
    {
        Schema::table('segments', function (Blueprint $table) {
            if (! Schema::hasColumn('segments', 'rulebook_path')) {
                $table->string('rulebook_path')->nullable()->after('description');
            }
            if (! Schema::hasColumn('segments', 'order')) {
                $table->unsignedSmallInteger('order')->default(0)->after('rulebook_path');
            }
        });

        if (! $this->hasIndex('segments', 'segments_hackathon_id_index')) {
            Schema::table('segments', function (Blueprint $table) {
                $table->index('hackathon_id');
            });
        }
    }

    protected function auditTeams(): void
    {
        if (Schema::hasColumn('teams', 'is_complete')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropColumn('is_complete');
            });
        }

        Schema::table('teams', function (Blueprint $table) {
            if (! Schema::hasColumn('teams', 'is_banned')) {
                $table->boolean('is_banned')->default(false)->after('invite_code');
            }
            if (! Schema::hasColumn('teams', 'banned_at')) {
                $table->timestamp('banned_at')->nullable()->after('is_banned');
            }
            if (! Schema::hasColumn('teams', 'banned_reason')) {
                $table->string('banned_reason')->nullable()->after('banned_at');
            }
            if (! Schema::hasColumn('teams', 'banned_by')) {
                $table->foreignId('banned_by')->nullable()->after('banned_reason')->constrained('users')->nullOnDelete();
            }
        });

        if (! $this->hasIndex('teams', 'teams_hackathon_id_index')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->index('hackathon_id');
            });
        }
        if (! $this->hasIndex('teams', 'teams_is_banned_index')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->index('is_banned');
            });
        }
    }

    protected function auditTeamMembers(): void
    {
        if (! $this->hasIndex('team_members', 'team_members_team_id_index')) {
            Schema::table('team_members', function (Blueprint $table) {
                $table->index('team_id');
            });
        }
        if (! $this->hasIndex('team_members', 'team_members_user_id_index')) {
            Schema::table('team_members', function (Blueprint $table) {
                $table->index('user_id');
            });
        }
    }

    protected function auditSubmissions(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('submissions', 'segment_id')) {
                $table->foreignId('segment_id')->nullable()->after('hackathon_id')->constrained('segments')->nullOnDelete();
            }
            if (! Schema::hasColumn('submissions', 're_open_submission')) {
                $table->boolean('re_open_submission')->default(false)->after('submitted_at');
            }
            if (! Schema::hasColumn('submissions', 'disqualified')) {
                $table->boolean('disqualified')->default(false)->after('re_open_submission');
            }
            if (! Schema::hasColumn('submissions', 'disqualified_reason')) {
                $table->string('disqualified_reason')->nullable()->after('disqualified');
            }
            if (! Schema::hasColumn('submissions', 'disqualified_by')) {
                $table->foreignId('disqualified_by')->nullable()->after('disqualified_reason')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('submissions', 'disqualified_at')) {
                $table->timestamp('disqualified_at')->nullable()->after('disqualified_by');
            }
        });

        if (! $this->hasIndex('submissions', 'submissions_hackathon_id_index')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->index('hackathon_id');
            });
        }
        if (! $this->hasIndex('submissions', 'submissions_team_id_index')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->index('team_id');
            });
        }
        if (! $this->hasIndex('submissions', 'submissions_is_draft_index')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->index('is_draft');
            });
        }
        if (! $this->hasIndex('submissions', 'submissions_disqualified_index')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->index('disqualified');
            });
        }

        if (Schema::hasColumn('submissions', 'segment_id')) {
            DB::table('submissions')->whereNull('segment_id')->orderBy('id')->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $segmentId = DB::table('teams')->where('id', $row->team_id)->value('segment_id');
                    DB::table('submissions')->where('id', $row->id)->update(['segment_id' => $segmentId]);
                }
            });
        }
    }

    protected function auditSubmissionFiles(): void
    {
        if (Schema::hasColumn('submission_files', 'file_size') && ! Schema::hasColumn('submission_files', 'file_size_kb')) {
            Schema::table('submission_files', function (Blueprint $table) {
                $table->unsignedInteger('file_size_kb')->default(0)->after('original_name');
            });

            DB::table('submission_files')->orderBy('id')->chunk(200, function ($rows) {
                foreach ($rows as $row) {
                    $bytes = (int) ($row->file_size ?? 0);
                    $kb = (int) max(0, ceil($bytes / 1024));
                    DB::table('submission_files')->where('id', $row->id)->update(['file_size_kb' => $kb]);
                }
            });

            Schema::table('submission_files', function (Blueprint $table) {
                $table->dropColumn('file_size');
            });
        } elseif (! Schema::hasColumn('submission_files', 'file_size_kb')) {
            Schema::table('submission_files', function (Blueprint $table) {
                $table->unsignedInteger('file_size_kb')->default(0)->after('original_name');
            });
        }
    }

    protected function auditAnnouncements(): void
    {
        $addedStatus = false;
        if (! Schema::hasColumn('announcements', 'status')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->enum('status', ['draft', 'scheduled', 'published'])->default('draft')->after('published_at');
            });
            $addedStatus = true;
        }

        if ($addedStatus) {
            DB::table('announcements')->whereNotNull('published_at')->update(['status' => 'published']);
            DB::table('announcements')->whereNull('published_at')
                ->whereNotNull('scheduled_at')
                ->update(['status' => 'scheduled']);
            DB::table('announcements')->whereNull('published_at')
                ->whereNull('scheduled_at')
                ->update(['status' => 'draft']);
        }

        if (! $this->hasIndex('announcements', 'announcements_hackathon_id_index')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->index('hackathon_id');
            });
        }
        if (! $this->hasIndex('announcements', 'announcements_status_index')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->index('status');
            });
        }
    }

    protected function auditScoringCriteria(): void
    {
        Schema::table('scoring_criteria', function (Blueprint $table) {
            if (! Schema::hasColumn('scoring_criteria', 'order')) {
                $table->unsignedSmallInteger('order')->default(0)->after('max_score');
            }
        });

        if (! $this->hasIndex('scoring_criteria', 'scoring_criteria_hackathon_id_index')) {
            Schema::table('scoring_criteria', function (Blueprint $table) {
                $table->index('hackathon_id');
            });
        }
    }

    protected function auditScores(): void
    {
        if (! $this->hasIndex('scores', 'scores_submission_id_index')) {
            Schema::table('scores', function (Blueprint $table) {
                $table->index('submission_id');
            });
        }
        if (! $this->hasIndex('scores', 'scores_criteria_id_index')) {
            Schema::table('scores', function (Blueprint $table) {
                $table->index('criteria_id');
            });
        }
    }

    protected function auditSponsors(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            if (! Schema::hasColumn('sponsors', 'order')) {
                $table->unsignedSmallInteger('order')->default(0)->after('tier');
            }
        });

        if (! $this->hasIndex('sponsors', 'sponsors_hackathon_id_index')) {
            Schema::table('sponsors', function (Blueprint $table) {
                $table->index('hackathon_id');
            });
        }
        if (! $this->hasIndex('sponsors', 'sponsors_tier_index')) {
            Schema::table('sponsors', function (Blueprint $table) {
                $table->index('tier');
            });
        }
    }

    protected function auditFaqs(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            if (! Schema::hasColumn('faqs', 'segment_id')) {
                $table->foreignId('segment_id')->nullable()->after('hackathon_id')->constrained('segments')->nullOnDelete();
            }
        });

        if (! $this->hasIndex('faqs', 'faqs_hackathon_id_index')) {
            Schema::table('faqs', function (Blueprint $table) {
                $table->index('hackathon_id');
            });
        }
    }

    protected function auditHackathonOrganizers(): void
    {
        if (! $this->hasIndex('hackathon_organizers', 'hackathon_organizers_user_id_index')) {
            Schema::table('hackathon_organizers', function (Blueprint $table) {
                $table->index('user_id');
            });
        }
    }

    protected function auditSettings(): void
    {
        if (! $this->hasIndex('settings', 'settings_key_index') && ! $this->hasIndex('settings', 'settings_key_unique')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->index('key');
            });
        }
    }

    protected function auditNotifications(): void
    {
        if (! $this->hasIndex('notifications', 'notifications_notifiable_type_notifiable_id_index')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['notifiable_type', 'notifiable_id']);
            });
        }
    }

    protected function addMissingIndexes(): void
    {
        // Covered in table-specific audit methods; reserved for any stragglers.
    }
};
