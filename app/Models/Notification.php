<?php

namespace App\Models;

use App\Notifications\AppNotification;
use Illuminate\Database\Eloquent\Model;

/**
 * Représente une notification système destinée aux utilisateurs.
 * Gère le contenu, la date de diffusion et le lien vers l'objet concerné.
 */
class Notification extends Model {
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = ['object_type', 'object_id', 'content_type', 'release_at'];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = ['release_at' => 'date:Y-m-d'];

    /**
     * Accesseur retournant le message de notification formaté.
     * Remplace le marqueur '@date' par la date de diffusion.
     *
     * @return string
     */
    public function getMessageAttribute() {
        return str_replace('@date', $this->release_at->format('d/m/Y'), AppNotification::MESSAGES[$this->content_type]);
    }

    /**
     * Accesseur retournant le lien (URL ou ancre) vers l'objet de la notification.
     *
     * @return string
     */
    public function getHRefAttribute() {
        switch ($this->content_type) {
            case 'NP' :
            case 'UP' :
            case 'CP' :
                return $this->object->route;
            case 'NA' :
            case 'UA' :
                return '#apps';
            case 'UO' :
                return 'rh.org-chart';
        }
    }

    /**
     * Relation vers les utilisateurs destinataires de cette notification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() {
        return $this->belongsToMany(User::class);
    }

    /**
     * Relation polymorphique vers l'objet concerné par la notification (ex: Post, App).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function object() {
        return $this->morphTo();
    }
}
