<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecialOrder extends Model {
 	 
	public $timestamps=true;
    protected $dates = ['deleted_at'];

    protected $table = 'special_order';

    protected $hidden = array('password', 'remember_token');

    protected $primaryKey = ['special_orderID']; // or null

    public $incrementing = false;

    protected $fillable = ['special_orderID','OrderNumber','dealerID','company_name','product_id','product_color','product_side_color','finance','attribute','orderstatus','color','comments','address','today_date'];

    protected $rules=[
        'id'=>'required'
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
