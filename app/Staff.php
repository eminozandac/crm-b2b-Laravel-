<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model {
 	 
	public $timestamps=true;
    protected $dates = ['deleted_at'];
    protected $table = 'staff';

    protected $hidden = array('password', 'remember_token');

    protected $primaryKey = 'staff_id'; // or null

    public $incrementing = false;

    protected $fillable = ['staff_id', 'first_name', 'last_name', 'emailID', 'password', 'phone', 'address', 'country', 'role', 'status', 'staffAvatar', 'created_at', 'updated_at', 'deleted_at'];


    protected $rules=[
        'id'=>'required',
        'staff_id'=>'required'
    ];

    public function save(array $options = [])
    {
        if( ! is_array($this->getKeyName()))
        {
            return parent::save($options);
        }

        // Fire Event for others to hook
        if($this->fireModelEvent('saving') === false) return false;

        // Prepare query for inserting or updating
        $query = $this->newQueryWithoutScopes();

        // Perform Update
        if ($this->exists)
        {
            if (count($this->getDirty()) > 0)
            {
                // Fire Event for others to hook
                if ($this->fireModelEvent('updating') === false)
                {
                    return false;
                }

                // Touch the timestamps
                if ($this->timestamps)
                {
                    $this->updateTimestamps();
                }

                //
                // START FIX
                //


                // Convert primary key into an array if it's a single value
                $primary = (count($this->getKeyName()) > 1) ? $this->getKeyName() : [$this->getKeyName()];

                // Fetch the primary key(s) values before any changes
                $unique = array_intersect_key($this->original, array_flip($primary));

                // Fetch the primary key(s) values after any changes
                $unique = !empty($unique) ? $unique : array_intersect_key($this->getAttributes(), array_flip($primary));

                // Fetch the element of the array if the array contains only a single element
                //$unique = (count($unique) <> 1) ? $unique : reset($unique);

                // Apply SQL logic
                $query->where($unique);

                //
                // END FIX
                //

                // Update the records
                $query->update($this->getDirty());

                // Fire an event for hooking into
                $this->fireModelEvent('updated', false);
            }
        }
        // Insert
        else
        {
            // Fire an event for hooking into
            if ($this->fireModelEvent('creating') === false) return false;

            // Touch the timestamps
            if($this->timestamps)
            {
                $this->updateTimestamps();
            }

            // Retrieve the attributes
            $attributes = $this->attributes;

            if ($this->incrementing && !is_array($this->getKeyName()))
            {
                $this->insertAndSetId($query, $attributes);
            }
            else
            {
                $query->insert($attributes);
            }

            // Set exists to true in case someone tries to update it during an event
            $this->exists = true;

            // Fire an event for hooking into
            $this->fireModelEvent('created', false);
        }

        // Fires an event
        $this->fireModelEvent('saved', false);

        // Sync
        $this->original = $this->attributes;

        // Touches all relations
        if (array_get($options, 'touch', true)) $this->touchOwners();

        return true;
    }

}
