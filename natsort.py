# copyright 2011-2012 Stefano Karapetsas <stefano@karapetsas.com>

# This file is part of AutoMate.
#
# AutoMate is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# AutoMate is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with AutoMate.  If not, see <http://www.gnu.org/licenses/>.

# ---------------------------------------------------------
# natsort.py: Natural string sorting.
# ---------------------------------------------------------

# By Seo Sanghyeon.  Some changes by Connelly Barnes & Spondon Saha

def try_int(s):
    "Convert to integer if possible."
    try: return int(s)
    except: return s

def natsort_key(s):
    "Used internally to get a tuple by which s is sorted."
    import re
    return map(try_int, re.findall(r'(\d+|\D+)', s))

def natcmp(a, b):
    "Natural string comparison, case sensitive."
    return cmp(natsort_key(a), natsort_key(b))

def natcasecmp(a, b):
    "Natural string comparison, ignores case."
    return natcmp(a.lower(), b.lower())

def natsort(seq, cmp=natcmp, reverse=False):
    "In-place natural string sort."
    if reverse:
        seq.sort(cmp, reverse=True)
    else:
        seq.sort(cmp)

def natsorted(seq, cmp=natcmp, reverse=False):
    "Returns a copy of seq, sorted by natural string sort."
    import copy
    temp = copy.copy(seq)
    natsort(temp, cmp, reverse)
    return temp
